<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\TrackSkill;
use App\Models\ClassSession;
use App\Models\ClassTemplate;
use App\Models\AgeSubcategory;
use App\Models\TrackEventLevel;
use Illuminate\Database\Seeder;
use App\Models\TrackEventCategory;
use Illuminate\Support\Collection;
use App\Models\SkillStudentProgress;
use Illuminate\Support\Facades\Hash;
use App\Models\StudentClassEnrolment;
use Database\Factories\StudentFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DemoSeeder extends Seeder
{
    /**
     */
    public function run(): void
    {        
        // wonderful student
        $goodStudent = Student::factory()->overrideAllowed()
            ->create([
                'user_id' => 2,
                'age_subcategory_id' => AgeSubcategory::where('key', 'nenchu')->value('id'),
                'name_hiragana' => 'うた けんもつ',
                'first_name_ja' => '詩',
                'last_name_ja' => '監物',
                'first_name_en' => 'uta',
                'last_name_en' => 'kenmotsu',
                'override_allowed' => true,
            ]);

        // troublesome student
        $troublesomeStudent = Student::factory()->troublesome()
            ->create([
                'user_id' => 2,
                'age_subcategory_id' => AgeSubcategory::where('key', 'nenchu')->value('id'),
                'name_hiragana' => 'さく けんもつ',
                'first_name_ja' => '朔',
                'last_name_ja' => '監物',
                'first_name_en' => 'saku',
                'last_name_en' => 'kenmotsu',
                'capacity_weight' => 2,
            ]);

        $this->seedProgresses($goodStudent, $troublesomeStudent);

        // Ticket pools (dummy students)
        $pools = $this->createTicketPools();

        // 2) Place both demo students into the SAME color-coded class session
        $this->placeSpecialStudentsInSameSession($goodStudent, $troublesomeStudent);

        // 3) Fill sessions to targets (8/9/10), starting from existing used units
        $sessions = ClassSession::with('classTemplate.eligibleAgeSubcategories')->get();

        foreach ($sessions as $session) {
            $eligibleSubcatIds = $session->classTemplate->eligibleAgeSubcategories->pluck('id')->all();
            $target = $this->getTargetUnits($session);

            $this->fillSessionToTargetUnits($session, $target, $pools, $eligibleSubcatIds);
        }
    }

    private function placeSpecialStudentsInSameSession(Student $good, Student $trouble): void
    {
        $labelJa = '幼児クラス（年少〜年長）';
        $session = $this->findSessionByLabelAndSlot($labelJa, 4, '15:10');

        // Enroll trouble first (weight=2) then good (weight=1)
        $this->enrolStudentIntoSession($trouble, $session);
        $this->enrolStudentIntoSession($good, $session);

        $goodSecondSession = $this->findSessionByLabelAndSlot($labelJa, 5, '15:10'); 
        $this->enrolStudentIntoSession($good, $goodSecondSession);
    }

    private function findSessionByLabelAndSlot(string $labelJa, int $dayOfWeek, string $startHHMM): ClassSession
    {
        // Load all sessions (small dataset) and filter in PHP to avoid SQLite date function headaches
        $sessions = ClassSession::with('classTemplate.eligibleAgeSubcategories')
            ->where('status', 'confirmed')
            ->get();

        $session = $sessions->first(function (ClassSession $s) use ($labelJa, $dayOfWeek, $startHHMM) {
            return $s->classTemplate?->label_ja === $labelJa
                && $s->date->dayOfWeek === $dayOfWeek
                && substr($s->start_time, 0, 5) === $startHHMM;
        });

        if (! $session) {
            throw new \RuntimeException("Could not find session: {$labelJa} day={$dayOfWeek} start={$startHHMM}");
        }

        return $session;
    }

    private function enrolStudentIntoSession(Student $student, ClassSession $session): void
    {
        // 1) eligibility check
        $eligible = $session->classTemplate->eligibleAgeSubcategories
            ->pluck('id')
            ->contains($student->age_subcategory_id);

        if (! $eligible) {
            throw new \RuntimeException("Student {$student->id} not eligible for session {$session->id}");
        }

        // 2) capacity check (units-aware)
        $usedUnits = StudentClassEnrolment::query()
            ->where('class_session_id', $session->id)
            ->where('status', 'booked')
            ->sum('capacity_units');

        $remaining = $session->capacity - $usedUnits;

        if ($remaining < $student->capacity_weight) {
            throw new \RuntimeException("Session {$session->id} does not have enough remaining units for student {$student->id}");
        }

        // 3) insert (idempotent)
        StudentClassEnrolment::firstOrCreate(
            [
                'class_session_id' => $session->id,
                'student_id' => $student->id,
            ],
            [
                'enrolment_date' => $session->date,
                'capacity_units' => $student->capacity_weight,
                'status' => 'booked',
            ]
        );
    }

    private function getTargetUnits(ClassSession $session): int
    {
        $day = $session->date->dayOfWeekIso; 
        $start = substr($session->start_time, 0, 5); // TIME 

        // 9/10: Sat 11:00 for the shared demo session
        if ($day === 7 && $start === '10:00') {
            return 6;
        }

        // 9/10: Thu 15:10, Sat 10:00
        if (($day === 7 && $start === '11:00') || ($day === 7 && $start === '14:20')) {
            return 9;
        }

        // 8/10: Tue 17:10, Thu 17:10
        if (($day === 2 && $start === '17:10') || ($day === 4 && $start === '17:10')) {
            return 7;
        }

        return 10;
    }

    private function createTicketPools(): array
    {
        $basePools = [
            'mishoenji' => 30,
            'nensho' => 30,
            'nenchu' => 36,
            'nencho' => 80,
            'low' => 48,
            'mid' => 40,
            'high' => 40,
        ];

        $user = User::factory()->create();
        $subcatMap = AgeSubcategory::pluck('id', 'key'); // key => id

        $ticketPools = [];

        foreach ($basePools as $key => $count) {
            $subcatId = $subcatMap->get($key);

            if (! $subcatId) {
                throw new \RuntimeException("Missing age_subcategory key: {$key}");
            }

            $students = Student::factory()
                ->count($count)
                ->create([
                    'user_id' => $user->id,
                    'age_subcategory_id' => $subcatId,
                    'capacity_weight' => 1, // keep dummy simple
                ]);

            $ids = $students->pluck('id')->all();

            // Convert to tickets:
            // Everyone gets 1 ticket, most get +1 extra ticket (2 sessions/week)
            $tickets = [];
            foreach ($ids as $id) {
                $tickets[] = $id;
                if (rand(1, 100) <= 70) { // 70% attend twice a week
                    $tickets[] = $id;
                }
            }

            shuffle($tickets);
            $ticketPools[$subcatId] = $tickets;
        }

        return $ticketPools;
    }

    private function fillSessionToTargetUnits(
        ClassSession $session,
        int $targetUnits,
        array &$pools,
        array $eligibleSubcategoryIds
    ): void {

        $usedUnits = StudentClassEnrolment::query()
            ->where('class_session_id', $session->id)
            ->where('status', 'booked')
            ->sum('capacity_units');

        if ($usedUnits >= $targetUnits) {
            return; // already at/over target
        }

        while ($usedUnits < $targetUnits) {
            $remaining = $targetUnits - $usedUnits;

            $pickedStudent = null;
            $pickedStudentId = null;

            foreach ($eligibleSubcategoryIds as $subcatId) {
                if (empty($pools[$subcatId])) {
                    continue;
                }

                // Try a few tickets from this pool until we find a valid student
                $attempts = 0;

                while (!empty($pools[$subcatId]) && $attempts < 10) {
                    $attempts++;

                    $candidateId = array_pop($pools[$subcatId]);
                    $candidate = Student::find($candidateId);

                    if (! $candidate) {
                        continue;
                    }

                    // prevent duplicate enrolment in same session
                    $already = StudentClassEnrolment::where('class_session_id', $session->id)
                        ->where('student_id', $candidateId)
                        ->exists();

                    if ($already) {
                        continue;
                    }

                    if ($candidate->capacity_weight > $remaining) {
                        $pools[$subcatId][] = $candidateId;
                        continue;
                    }

                    $pickedStudent = $candidate;
                    $pickedStudentId = $candidateId;
                    $pickedPoolId = $subcatId;
                    break 2; // break out of both loops
                }
            }

            if (! $pickedStudent) {
                throw new \RuntimeException("Not enough eligible tickets to fill session {$session->id} to {$targetUnits} units.");
            }

            StudentClassEnrolment::create([
                'class_session_id' => $session->id,
                'student_id' => $pickedStudentId,
                'enrolment_date' => $session->date,
                'capacity_units' => $pickedStudent->capacity_weight, // snapshot
                'status' => 'booked',
            ]);

            $usedUnits += $pickedStudent->capacity_weight;
        }
    }

    private function seedProgresses($goodStudent, $badStudent)
    {
        $categories = TrackEventCategory::whereIn('category_name', ['跳び箱', 'マット', '鉄棒'])->get()->keyBy('category_name');
        $categoriesInDan = TrackEventLevel::where('level_type', 'dan')->get()->groupBy('track_event_category_id');
        $kyuLevels = TrackEventLevel::where('level_type', 'kyu')->get()->groupBy('track_event_category_id');

        // DAN: mat, vault, bar
        foreach ($categoriesInDan as $category) {
            foreach ($category as $categoryLevel) {
                $skills = TrackSkill::where('track_event_level_id', $categoryLevel->id)->get();

                // Pass all dan levels for both demo students
                foreach ($skills as $skill) {
                    SkillStudentProgress::create([
                        'student_id' => $goodStudent->id,
                        'track_skill_id' => $skill->id,
                        'skill_name' => $skill->skill_label,
                        'has_passed' => true,
                    ]);

                    SkillStudentProgress::create([
                        'student_id' => $badStudent->id,
                        'track_skill_id' => $skill->id,
                        'skill_name' => $skill->skill_label,
                        'has_passed' => true,
                    ]);
                }
            }
        }

        // KYU: mat, vault, ** no vault **
        foreach ($categoriesInDan->splice(0, 2) as $category) {
            foreach ($category as $categoryLevel) {
                $skills = TrackSkill::where('track_event_level_id', $categoryLevel->id)->get();

                // Pass all kyu levels for both demo students
                foreach ($skills as $skill) {
                    SkillStudentProgress::create([
                        'student_id' => $goodStudent->id,
                        'track_skill_id' => $skill->id,
                        'skill_name' => $skill->skill_label,
                        'has_passed' => true,
                    ]);

                    SkillStudentProgress::create([
                        'student_id' => $badStudent->id,
                        'track_skill_id' => $skill->id,
                        'skill_name' => $skill->skill_label,
                        'has_passed' => true,
                    ]);
                }
            }
        }
        
    }
}

<?php

namespace App\Http\Controllers\v1;

use DateTimeZone;
use Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Student;
use Carbon\CarbonPeriod;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\StudentClassEnrolment;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $sessions = ClassSession::with('enrolments')->get();

        return response()->json([
            'data' => $sessions,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function currentSchedule(Request $request): JsonResponse
    {   
        $user = $request->user();

        $uta = Student::find(2); // uta
        // $saku = Student::find(1); // saku
        $enrolledSessionIds = StudentClassEnrolment::where('student_id', $uta->id)->where('status', 'booked')->pluck('class_session_id');
        $uta['enrolled_session_ids'] = $enrolledSessionIds;

        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();

        $sessionsThisWeek = ClassSession::with('classTemplate.eligibleAgeSubcategories')
            ->whereDate('date', '>=', $startOfWeek)
            ->whereDate('date', '<=', $endOfWeek)
            ->get();
    
        $sessions = $sessionsThisWeek->map(function (ClassSession $session) use ($uta): array {
            $template = $session->classTemplate;
            $spaceUsed = $session->enrolments()->where('status', 'booked')->sum('capacity_units');

            $eligibleAgeSubcategoryIds = $template?->eligibleAgeSubcategories
                    ->pluck('id')
                    ->values()
                    ->all() ?? [];
            $isEligible = in_array($uta->age_subcategory_id, $eligibleAgeSubcategoryIds, true);

            return [
                'id' => $session->id,
                'class_template_id' => $template?->id,
                'is_eligible' => $isEligible, 
                'label_en' => $template?->label_en,
                'label_ja' => $template?->label_ja,
                'date' => $session->date,
                'day_of_week' => $template?->day_of_week,
                'start_time' => substr($session->start_time, 0, 5),
                'end_time' => substr($session->end_time, 0, 5),
                'capacity' => $session->capacity,
                'capacity_used' => $spaceUsed,
                'allow_capacity_override' => $session->allow_capacity_override,
                'status' => $session->status,
            ];
        });

        $data = [
            'start' => $startOfWeek,
            'end' => $endOfWeek,
            'class_sessions' => $sessions,
        ];

        return response()->json([
            'data' => $data,
            'context' => [
                'user' => $user->load('students'),
                'student' => $uta, // TODO:: only return needed data
            ],
        ], 200);
    }

    public function switchClassSessions(Request $request): JsonResponse
    {   
        // use DB::transaction because double writing in this function
        $student = Student::find($request->studentId);

        $currentClassEnrolment = StudentClassEnrolment::where([
            'student_id' => $student->id,
            'class_session_id' => $request->fromSessionId,
        ])->firstOrFail();

        $toClassSession = ClassSession::where('id', $request->toSessionId)->first();

        $canJoin = $this->checkIfCanJoinClass($toClassSession, $student->capacity_weight, $student->override_allowed);
        if (!$canJoin) {
            return response()->json([
                'message' => 'Class is full (or capacity override not allowed).',
                'data' => null,
            ], 409);
        }

        $currentClassEnrolment->update([
            'class_session_id' => $toClassSession->id,
            'enrolment_date' => Carbon::today('Asia/Tokyo')->toDateTimeString(),
        ]);

        $student->reschedules_used++;
        $student->save();

        return response()->json([
            'message' => "Class sessions switched successfully.",
            'data' => $currentClassEnrolment,
        ], 201);    
    }

    private function checkIfCanJoinClass(ClassSession $classToJoin, int $weight, bool $canOverride): bool
    {   
        $capacity = $classToJoin->capacity;

        $usedUnits = StudentClassEnrolment::where([
            'class_session_id' => $classToJoin->id,
            'status' => 'booked',
        ])->sum('capacity_units');

        $spaceLeft = $capacity - $usedUnits;
        $canJoin = $weight <= $spaceLeft;

        if ($canJoin) return true;

        return $canOverride && $classToJoin->allow_capacity_override;
    }

    public function user()
    {
        $user = User::with('students.classEnrolments.classSession.classTemplate')->find(2);

        $students = $user->students;
        foreach ($students as $student) {
            foreach ($student->classEnrolments as $class) {
                $className = explode("（", $class->classSession->classTemplate->label_ja);
                $dayOfWeek = $class->classSession->classTemplate->day_of_week;
                $class['class_name'] = $className[0];
                $class['day_of_week'] = $dayOfWeek;
            }
        }

        return response()->json([
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'phoneNumber' => $user->phone_number,
                'students' => $students,
            ]
        ], 200);
    }
}

<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Location;
use Carbon\CarbonPeriod;
use App\Models\ClassTemplate;
use App\Models\AgeSubcategory;
use App\Models\ClassSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ScheduleSeeder extends Seeder
{   
    private array $dayMap = [
        'mon' => 1,
        'tue' => 2,
        'wed' => 3,
        'thu' => 4,
        'fri' => 5,
        'sat' => 6,
        'sun' => 7
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $location = Location::first();
        $jsonContent = File::get(database_path('/seeders/data/class_templates.json'));
        $data = json_decode($jsonContent, true);

        $templateData = $data['class_templates'];

        foreach ($templateData as $template) {
            $days = $template['days'];
            $eligibleSubcategoryIds = AgeSubcategory::whereIn('key', $template['eligible_age_subcategory_keys'])
                        ->pluck('id');

            // create a template for each unique day
            foreach ($days as $day) {
                $dayNumeric = $this->dayMap[$day];
                $classTemplate = ClassTemplate::create([
                    'location_id' => $location->id,
                    'label_en' => $template['label_en'],
                    'label_ja' => $template['label_ja'],
                    'day_of_week' => $dayNumeric,
                    'default_capacity' => $template['default_capacity'],
                    'start_time' => $template['start_time'],
                    'end_time' => $template['end_time'],
                ]);
                
                $classTemplate->eligibleAgeSubcategories()->sync($eligibleSubcategoryIds);
            }
        }

        $this->seedOneWeekClassSessions();
    }

    private function seedOneWeekClassSessions(): void
    {   
        // From Mon-Sun
        $daysOfWeek = CarbonPeriod::create(
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        );

        foreach ($daysOfWeek as $day) {
            // retrieve all templates for this day
            $templates = ClassTemplate::where('day_of_week', $day->dayOfWeekIso)->get();

            // create class sessions using templates
            foreach ($templates as $template) {
                ClassSession::create([
                    'class_template_id' => $template->id,
                    'date' => $day->toDateString(),
                    'start_time' => $template->start_time,
                    'end_time' => $template->end_time,
                    'capacity' => $template->default_capacity,
                    'allow_capacity_override' => true,
                    'status' => 'confirmed',
                ]);
            }
        }
    }
}

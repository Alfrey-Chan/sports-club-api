<?php

namespace Database\Seeders;

use App\Models\AgeCategory;
use App\Models\AgeSubcategory;
use App\Models\TrackEventCategory;
use App\Models\TrackEventLevel;
use App\Models\TrackSkill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use PhpParser\Node\Scalar\String_;

class CurriculumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonContent = File::get(database_path('/seeders/data/age_categories.json'));
        $data = json_decode($jsonContent, true);

        $ageCategoriesData = $data['age_categories'];
        $this->seedCategories($ageCategoriesData);
        
        $trackData = $data['track_categories'];
        $this->seedTrackCategories($trackData);
    }

    private function seedCategories(array $categoryData): void
    {
        foreach ($categoryData as $category) {
            $categoryName = $category['name_ja'];
            $ageCategory = AgeCategory::create(['name_ja' => $categoryName]);

            foreach ($category['subcategories'] as $subcategory) {
                AgeSubcategory::create([
                    'age_category_id' => $ageCategory['id'],
                    'key' => $subcategory['key'],
                    'name_ja' => $subcategory['name_ja'],
                    'min_age' => $subcategory['min_age'],
                    'max_age' => $subcategory['max_age'],
                ]);
            }
        }
    }

    private function seedTrackCategories(array $trackCategoryData): void
    {   
        foreach ($trackCategoryData as $track) {
            $trackEvent = TrackEventCategory::create([
                'category_name' => $track['name_jp'],
            ]);

            $levels = $track['levels'];
            // dd($levels);
            $danLevels = $levels['levels_beginner'];
            $kyuLevels = $levels['levels_standard'];

            foreach ($danLevels as $dan) {
                    $trackLevel = TrackEventLevel::create([
                        'track_event_category_id' => $trackEvent->id,
                        'level_type' => 'dan',
                        'level' => $dan['lvl'], 
                        'label' => $dan['name_ja'],
                    ]);

                    $skills = $dan['skills'];
                    foreach ($skills as $skill) {
                        TrackSkill::create([
                            'track_event_level_id' => $trackLevel->id,
                            'skill_label' => $skill['description_ja'],
                        ]);
                    }
                    
                }

                foreach ($kyuLevels as $kyu) {
                    $trackLevel = TrackEventLevel::create([
                        'track_event_category_id' => $trackEvent->id,
                        'level_type' => 'kyu',
                        'level' => $kyu['lvl'], 
                        'label' => $kyu['name_ja'],
                    ]);

                    $skills = $kyu['skills'];
                    foreach ($skills as $skill) {
                        TrackSkill::create([
                            'track_event_level_id' => $trackLevel->id,
                            'skill_label' => $skill['description_ja'],
                        ]);
                    }
                }
        }
    }

    
}

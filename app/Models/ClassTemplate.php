<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClassTemplate extends Model
{
    protected $fillable = [
        'location_id',
        'label_en',
        'label_ja',
        'day_of_week',
        'start_time',
        'end_time',
        'default_capacity',
    ];

    public function eligibleAgeSubcategories(): BelongsToMany
    {
        return $this->belongsToMany(AgeSubcategory::class, 'class_template_eligible_age_subcategories');
    }
}

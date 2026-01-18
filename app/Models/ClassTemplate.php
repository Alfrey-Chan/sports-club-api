<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassTemplate extends Model
{
    protected $fillable = [
        "location_id",
        "age_subcategory_id",
        "day_of_week",
        "start_time",
        "end_time",
        "default_capacity",
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];
}

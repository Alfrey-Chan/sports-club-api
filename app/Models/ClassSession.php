<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    protected $fillable = [
        'class_template_id',
        'date',
        'start_time',
        'end_time',
        'capacity',
        'allow_capacity_override',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'allow_capacity_override' => 'boolean',
    ];
}

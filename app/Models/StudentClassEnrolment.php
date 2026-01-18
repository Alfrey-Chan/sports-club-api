<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentClassEnrolment extends Model
{
    protected $fillable = [
        'class_session_id',
        'student_id',
        'enrollment_date',
        'capacity_units',
        'override_capacity',
        'status',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'override_capacity' => 'boolean',
    ];
}

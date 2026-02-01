<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentClassEnrolment extends Model
{
    protected $fillable = [
        'class_session_id',
        'student_id',
        'enrolment_date',
        'capacity_units',
        // 'override_capacity',
        'status',
    ];

    protected $casts = [
        'enrolment_date' => 'date',
        // 'override_capacity' => 'boolean',
    ];

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsto(Student::class);
    }
}

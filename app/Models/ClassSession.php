<?php

namespace App\Models;

use App\Models\StudentClassEnrolment;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'allow_capacity_override' => 'boolean',
    ];
    public function classTemplate(): BelongsTo
    {
        return $this->belongsTo(ClassTemplate::class);
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(StudentClassEnrolment::class);
    }
}

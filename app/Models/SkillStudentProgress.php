<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillStudentProgress extends Model
{
    protected $fillable = ['student_id', 'track_skill_id', 'skill_name', 'has_passed'];

    protected $casts = [
        'has_passed' => 'boolean',
    ];
}

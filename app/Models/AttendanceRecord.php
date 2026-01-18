<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'class_session_id',
        'marked_by_employee_id',
        'student_id',
        'status',
    ];
}

<?php

namespace App\Models;

use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;
    protected $fillable = ["user_id", "age_subcategory_id", "name_hiragana", "first_name_ja", "last_name_ja", "first_name_en", "last_name_en", "enrollment_date", "date_of_birth", "capacity_weight", "override_allowed", "monthly_reschedule_limit", "reschedules_used", "employee_note"]; 

    protected $casts = [
        "override_allowed" => "boolean",
        "date_of_birth" => "date",
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class);
    }

    public function classEnrolments(): HasMany
    {
        return $this->hasMany(StudentClassEnrolment::class);
    }
}

<?php

namespace App\Models;

use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    protected $fillable = ["user_id", "first_name", "last_name", "enrollment_date", "birthday", "capacity_weight", "override_allowed", "monthly_reschedule_limit", "employee_notes"]; // TODO:: employee_note should be a separate table to track who wrote the note, etc

    // public function classSessionByDate(): HasOne
    // {}
    public function account(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class);
    }
}

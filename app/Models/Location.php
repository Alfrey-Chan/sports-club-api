<?php

namespace App\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    protected $fillable = ["name"];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }
}

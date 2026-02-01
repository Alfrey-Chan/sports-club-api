<?php

namespace App\Models;

use App\Models\TrackSkill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrackEventLevel extends Model
{
    protected $fillable = ['track_event_category_id', 'level_type', 'level', 'label'];

    public function trackSkills(): HasMany
    {
        return $this->hasMany(TrackSkill::class);
    }
}

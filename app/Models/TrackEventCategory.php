<?php

namespace App\Models;

use App\Models\TrackEventLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrackEventCategory extends Model
{
    protected $fillable = ['category_name'];

    public function trackEventLevels(): HasMany
    {
        return $this->hasMany(TrackEventLevel::class);
    }
}

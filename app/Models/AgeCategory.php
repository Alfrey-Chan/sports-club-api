<?php

namespace App\Models;

use App\Models\AgeSubcategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgeCategory extends Model
{
    protected $fillable = ["name_ja"];

    public function ageSubcategories(): HasMany
    {
        return $this->hasMany(AgeSubcategory::class);
    }
}

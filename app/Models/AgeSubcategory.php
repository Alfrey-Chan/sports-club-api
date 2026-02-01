<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AgeSubcategory extends Model
{
    protected $fillable = ["age_category_id", "key", "name_ja", "min_age", "max_age"];

    public function classTemplates(): BelongsToMany
    {
        return $this->belongsToMany(ClassTemplate::class, 'class_template_eligible_age_subcategories');
    }
}

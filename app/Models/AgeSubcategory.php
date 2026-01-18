<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgeSubcategory extends Model
{
    protected $fillable = ["age_category_id", "category", "min_age", "max_age"];
}

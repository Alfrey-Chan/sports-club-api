<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('age_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId("age_category_id")->constrained()->cascadeOnDelete();
            $table->string("key");
            $table->string("name_ja")->unique();
            $table->smallInteger("min_age");
            $table->smallInteger("max_age");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('age_subcategories');
    }
};

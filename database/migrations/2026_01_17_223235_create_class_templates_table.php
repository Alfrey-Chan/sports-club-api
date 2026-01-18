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
        Schema::create('class_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId("location_id")->constrained();
            $table->foreignId("age_subcategory_id")->constrained()->cascadeOnDelete();
            $table->smallInteger("day_of_week");
            $table->smallInteger("default_capacity");
            $table->dateTime("start_time");
            $table->dateTime("end_time");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_templates');
    }
};

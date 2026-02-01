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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->foreignId('age_subcategory_id')->constrained();
            $table->string("name_hiragana");
            $table->string("first_name_ja");
            $table->string("last_name_ja");
            $table->string("first_name_en");
            $table->string("last_name_en");
            $table->string("employee_note")->nullable();
            $table->date("enrollment_date");
            $table->date("date_of_birth");
            $table->boolean("override_allowed")->default(false);
            $table->tinyInteger("capacity_weight")->default(1);
            $table->tinyInteger("monthly_reschedule_limit")->default(1);
            $table->tinyInteger("reschedules_used")->default(0);
            $table->timestamps();

            $table->index('age_subcategory_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

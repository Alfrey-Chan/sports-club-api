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
            $table->string("first_name");
            $table->string("last_name");
            $table->string("employee_note");
            $table->date("enrollment_date");
            $table->date("date_of_birth");
            $table->boolean("override_allowed")->default(false);
            $table->smallInteger("capacity_weight")->default(1);
            $table->smallInteger("monthly_reschedule_limit")->default(1);
            $table->timestamps();
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

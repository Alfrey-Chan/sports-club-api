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
        Schema::create('student_class_enrolments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('enrolment_date');
            $table->smallInteger('capacity_units')->default(1);
            // $table->boolean('override_capacity')->default(false);
            $table->enum('status', ['booked', 'cancelled', 'moved'])->default('booked');
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['class_session_id', 'status']);
            $table->index(['class_session_id', 'capacity_units']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_class_enrolments');
    }
};

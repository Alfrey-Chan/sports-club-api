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
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_template_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->smallInteger('capacity');
            $table->boolean('allow_capacity_override')->default(true);
            $table->enum('status', ['normal', 'cancelled', 'holiday']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};

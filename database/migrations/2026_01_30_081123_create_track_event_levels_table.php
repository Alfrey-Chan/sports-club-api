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
        Schema::create('track_event_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_event_category_id')->constrained()->cascadeOnDelete();
            $table->enum('level_type', ['kyu', 'dan']);
            $table->tinyInteger('level');
            $table->string('label');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('track_event_levels');
    }
};

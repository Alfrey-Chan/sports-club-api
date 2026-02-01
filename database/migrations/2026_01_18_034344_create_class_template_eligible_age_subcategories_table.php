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
        Schema::create('class_template_eligible_age_subcategories', function (Blueprint $table) {
            $table->foreignId('class_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('age_subcategory_id')->constrained()->cascadeOnDelete();

            $table->primary(['class_template_id', 'age_subcategory_id']);
            $table->index('age_subcategory_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_template_eligible_age_subcategories');
    }
};

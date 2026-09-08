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
        Schema::create('exercise_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['stretching', 'strength', 'cardio', 'rehabilitation', 'yoga'])->default('stretching');
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->integer('duration_minutes')->default(20);
            $table->json('exercises')->nullable();
            $table->integer('sessions_per_week')->default(3);
            $table->string('video_url')->nullable();
            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_programs');
    }
};

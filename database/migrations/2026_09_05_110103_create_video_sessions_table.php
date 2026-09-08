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
        Schema::create('video_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->string('room_name')->unique();
            $table->string('room_url');
            $table->string('doctor_token')->nullable();
            $table->string('patient_token')->nullable();
            $table->enum('provider', ['daily', 'whereby', 'jitsi'])->default('daily');
            $table->enum('status', ['waiting', 'active', 'ended'])->default('waiting');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_sessions');
    }
};

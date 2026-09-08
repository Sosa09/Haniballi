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
        Schema::table('video_call_signals', function (Blueprint $table): void {
            $table->string('client_id')->nullable()->after('sender_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_call_signals', function (Blueprint $table): void {
            $table->dropColumn('client_id');
        });
    }
};

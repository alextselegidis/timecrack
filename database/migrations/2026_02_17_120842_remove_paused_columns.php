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
        Schema::table('trackings', function (Blueprint $table) {
            $table->dropColumn('paused_duration');
        });

        Schema::table('active_trackings', function (Blueprint $table) {
            $table->dropColumn(['paused_at', 'paused_duration']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trackings', function (Blueprint $table) {
            $table->unsignedInteger('paused_duration')->default(0);
        });

        Schema::table('active_trackings', function (Blueprint $table) {
            $table->timestamp('paused_at')->nullable();
            $table->unsignedInteger('paused_duration')->default(0);
        });
    }
};

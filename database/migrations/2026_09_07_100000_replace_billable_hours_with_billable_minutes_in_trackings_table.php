<?php

/* ----------------------------------------------------------------------------
 * Timecrack - Time Tracking Application
 *
 * @package     Timecrack
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://github.com/alextselegidis/timecrack
 * ---------------------------------------------------------------------------- */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Billable time was kept as decimal hours, a lossy unit that never divided evenly into the
     * whole minutes the application shows, which is what a long line of repair migrations kept
     * reconciling. Whole minutes are the unit everything is rounded to anyway, so store them.
     */
    public function up(): void
    {
        Schema::table('trackings', function (Blueprint $table) {
            $table->unsignedInteger('billable_minutes')->nullable()->after('ended_at');
        });

        // Backfill with the same arithmetic the accessor used, so no displayed value changes.
        DB::statement(
            'UPDATE trackings SET billable_minutes = LEAST('
            . ' GREATEST(0, ROUND(TIMESTAMPDIFF(SECOND, started_at, ended_at) / 60)),'
            . ' GREATEST(0, ROUND(billable_hours * 60))'
            . ') WHERE billable_hours IS NOT NULL'
        );

        Schema::table('trackings', function (Blueprint $table) {
            $table->dropColumn('billable_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trackings', function (Blueprint $table) {
            $table->decimal('billable_hours', 8, 2)->nullable()->after('ended_at');
        });

        DB::statement(
            'UPDATE trackings SET billable_hours = ROUND(billable_minutes / 60, 2)'
            . ' WHERE billable_minutes IS NOT NULL'
        );

        Schema::table('trackings', function (Blueprint $table) {
            $table->dropColumn('billable_minutes');
        });
    }
};

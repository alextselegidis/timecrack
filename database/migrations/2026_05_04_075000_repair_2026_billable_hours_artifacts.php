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
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Repair billable_hours artefacts on 2026 trackings caused by the Stop
     * Timer modal pre-fill bug and DECIMAL(5,2) quantisation.
     *
     * For every tracking on or after 2026-01-01:
     *   - If the duration is shorter than a minute, force billable_hours to 0
     *     (those are accidental timer stops and must not be billed).
     *   - Otherwise, if billable_hours is positive but below the rounded
     *     duration, snap it up to ROUND(duration/3600, 2) so the totals query
     *     reports no leftover non-billable seconds.
     *
     * Rows already at 0 (intentionally non-billable) and rows where the user
     * deliberately billed >= the rounded duration are left untouched.
     */
    public function up(): void
    {
        DB::statement("
            UPDATE trackings
            SET billable_hours = 0
            WHERE started_at >= '2026-01-01 00:00:00'
              AND billable_hours IS NOT NULL
              AND billable_hours > 0
              AND TIMESTAMPDIFF(SECOND, started_at, ended_at) < 60
        ");

        DB::statement("
            UPDATE trackings
            SET billable_hours = ROUND(TIMESTAMPDIFF(SECOND, started_at, ended_at) / 3600, 2)
            WHERE started_at >= '2026-01-01 00:00:00'
              AND billable_hours IS NOT NULL
              AND billable_hours > 0
              AND TIMESTAMPDIFF(SECOND, started_at, ended_at) >= 60
              AND billable_hours < ROUND(TIMESTAMPDIFF(SECOND, started_at, ended_at) / 3600, 2)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot restore previous billable_hours values
    }
};

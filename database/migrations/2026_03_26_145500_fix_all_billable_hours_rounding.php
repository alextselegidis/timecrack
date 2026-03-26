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
     * Re-round all billable_hours values using ROUND instead of the
     * previously used FLOOR, so that billable matches the displayed duration
     * and totals are consistent.
     */
    public function up(): void
    {
        DB::statement('
            UPDATE trackings
            SET billable_hours = ROUND(TIMESTAMPDIFF(SECOND, started_at, ended_at) / 3600, 2)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot restore previous billable_hours values
    }
};

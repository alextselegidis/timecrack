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
     * Set billable_hours to the duration (in hours) between started_at and ended_at for all trackings.
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

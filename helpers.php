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

use App\Models\Setting;
use Carbon\Carbon;

if (!function_exists('sort_link')) {
    function sort_link($column, $label): string
    {
        $direction = request('sort') === $column && request('direction') === 'asc' ? 'desc' : 'asc';
        $url = request()->fullUrlWithQuery(['sort' => $column, 'direction' => $direction]);
        $icon = '<i class="bi ' . ($direction === 'asc' ? 'bi-caret-up' : 'bi-caret-down') . ' ms-2"></i>';
        return '<a href="' . $url . '">' . $label . $icon . '</a>';
    }
}

if (!function_exists('setting')) {
    function setting(array|string|null $key = null, mixed $default = null): mixed
    {
        if (empty($key)) {
            throw new InvalidArgumentException('The $key argument cannot be empty.');
        }

        if (is_array($key)) {
            foreach ($key as $name => $value) {
                $setting = Setting::query()->where('name', $name)->first();

                if (empty($setting)) {
                    $setting = new Setting([
                        'name' => $name,
                    ]);
                }

                $setting->value = $value;

                $setting->save();
            }

            return null;
        }

        $setting = Setting::query()->where('name', $key)->first() ?? null;

        return $setting->value ?? $default;
    }
}

if (!function_exists('user_timezone')) {
    /**
     * Get the timezone of the currently authenticated user, falling back to the default one.
     */
    function user_timezone(): string
    {
        static $timezones = [];

        $user = auth()->user();

        return $timezones[$user?->id ?? 0] ??= $user?->timezone ?: setting('default_timezone', 'UTC');
    }
}

if (!function_exists('tz')) {
    /**
     * Convert a stored (UTC) date time to the timezone of the current user, for display purposes.
     */
    function tz(mixed $value = null): ?Carbon
    {
        return $value ? Carbon::parse($value)->setTimezone(user_timezone()) : null;
    }
}

if (!function_exists('duration_label')) {
    /**
     * Format a number of minutes as "1h 30m", the only duration format the application shows.
     */
    function duration_label(int $minutes): string
    {
        return intdiv($minutes, 60) . 'h ' . $minutes % 60 . 'm';
    }
}

if (!function_exists('duration_hours')) {
    /**
     * Format a number of minutes as decimal hours, the format the CSV export and the tooltips use.
     */
    function duration_hours(int $minutes, string $thousands = ','): string
    {
        return number_format($minutes / 60, 2, '.', $thousands);
    }
}

if (!function_exists('billable_minutes')) {
    /**
     * Convert the decimal hours a form submits into the whole minutes the database stores.
     */
    function billable_minutes(mixed $hours): ?int
    {
        return $hours === null || $hours === '' ? null : max(0, (int) round((float) $hours * 60));
    }
}

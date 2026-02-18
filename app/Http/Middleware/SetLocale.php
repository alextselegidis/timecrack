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

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = setting('default_locale', config('app.locale', 'en'));

        // Determine the translation locale (check if JSON file exists, otherwise fallback)
        $translationLocale = $this->resolveTranslationLocale($locale);

        // Set the app locale to the full locale for HTML lang attribute and date formatting
        app()->setLocale($locale);

        // Override the translator locale to use the resolved translation file
        app('translator')->setLocale($translationLocale);

        return $next($request);
    }

    /**
     * Resolve which translation locale to use based on available JSON files.
     */
    private function resolveTranslationLocale(string $locale): string
    {
        $langPath = lang_path();

        // Check if exact locale JSON file exists (e.g., en-GB.json)
        if (file_exists($langPath . '/' . $locale . '.json')) {
            return $locale;
        }

        // Fallback to base language (e.g., en-GB -> en)
        if (str_contains($locale, '-')) {
            $baseLocale = explode('-', $locale)[0];
            if (file_exists($langPath . '/' . $baseLocale . '.json')) {
                return $baseLocale;
            }
        }

        // Final fallback to English
        return 'en';
    }
}

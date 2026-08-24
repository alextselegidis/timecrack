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

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AboutController extends Controller
{
    private const FEED_URL = 'https://timecrack.org/rss.xml';

    private const CACHE_KEY = 'about.blog_posts';

    private const POST_COUNT = 5;

    public function index()
    {
        return view('pages.about', ['posts' => $this->blogPosts()]);
    }

    /**
     * The latest blog posts of timecrack.org, cached for a day.
     *
     * A failed or unparsable response is not cached, so the next visit tries again, and the
     * about page simply renders without the blog section.
     */
    private function blogPosts(): array
    {
        $cached = Cache::get(self::CACHE_KEY);

        if ($cached !== null) {
            return $cached;
        }

        $posts = $this->fetchBlogPosts();

        if ($posts) {
            Cache::put(self::CACHE_KEY, $posts, now()->addDay());
        }

        return $posts;
    }

    private function fetchBlogPosts(): array
    {
        try {
            $response = Http::timeout(5)->get(self::FEED_URL);

            if (!$response->successful()) {
                return [];
            }

            $feed = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOERROR | LIBXML_NOWARNING);

            if ($feed === false) {
                return [];
            }

            $posts = [];

            foreach ($feed->channel->item ?? [] as $item) {
                $posts[] = [
                    'title' => trim((string) $item->title),
                    'link' => trim((string) $item->link),
                    'description' => trim((string) $item->description),
                    'published_at' => Carbon::parse((string) $item->pubDate),
                ];
            }

            // The feed is not sorted, the newest posts come first on the about page.
            usort($posts, fn(array $a, array $b) => $b['published_at'] <=> $a['published_at']);

            return array_slice($posts, 0, self::POST_COUNT);
        } catch (Throwable $exception) {
            Log::warning('Could not load the blog posts of the about page: ' . $exception->getMessage());

            return [];
        }
    }
}

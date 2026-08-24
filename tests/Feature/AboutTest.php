<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AboutTest extends TestCase
{
    use RefreshDatabase;

    private const FEED = <<<'XML'
        <?xml version="1.0" encoding="UTF-8"?>
        <rss version="2.0"><channel>
            <item>
                <title>Older post</title>
                <link>https://timecrack.org/blog/older/</link>
                <description>The older one.</description>
                <pubDate>Tue, 14 Apr 2026 22:00:00 GMT</pubDate>
            </item>
            <item>
                <title>Newer post</title>
                <link>https://timecrack.org/blog/newer/</link>
                <description>The newer one.</description>
                <pubDate>Tue, 07 Jul 2026 22:00:00 GMT</pubDate>
            </item>
        </channel></rss>
        XML;

    public function test_about_page_lists_the_blog_posts_newest_first(): void
    {
        Http::fake(['timecrack.org/*' => Http::response(self::FEED)]);

        $response = $this->actingAs(User::factory()->create())->get('/about');

        $response->assertOk();
        $response->assertSeeInOrder(['Newer post', 'Older post']);
    }

    public function test_about_page_renders_without_blog_posts_when_the_feed_is_unavailable(): void
    {
        Http::fake(['timecrack.org/*' => Http::response('', 500)]);

        $response = $this->actingAs(User::factory()->create())->get('/about');

        $response->assertOk();
        $response->assertDontSee('From the blog');
    }
}

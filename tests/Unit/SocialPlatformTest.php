<?php

namespace Tests\Unit;

use App\Support\SocialPlatform;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SocialPlatformTest extends TestCase
{
    #[DataProvider('urlProvider')]
    public function test_it_detects_the_platform_from_a_url(string $url, string $expectedKey): void
    {
        $this->assertSame($expectedKey, SocialPlatform::detect($url)['key']);
    }

    public static function urlProvider(): array
    {
        return [
            'twitter' => ['https://twitter.com/laravelphp', 'twitter'],
            'x.com' => ['https://x.com/laravelphp', 'twitter'],
            'instagram' => ['https://www.instagram.com/laravelphp', 'instagram'],
            'github' => ['https://github.com/laravel', 'github'],
            'linkedin' => ['https://www.linkedin.com/in/someone', 'linkedin'],
            'facebook' => ['https://facebook.com/laravel', 'facebook'],
            'youtube' => ['https://www.youtube.com/@laravel', 'youtube'],
            'youtu.be' => ['https://youtu.be/abc123', 'youtube'],
            'tiktok' => ['https://www.tiktok.com/@laravel', 'tiktok'],
            'threads' => ['https://www.threads.net/@laravel', 'threads'],
            'unknown domain falls back to generic link' => ['https://example.com/laravel', 'link'],
        ];
    }
}

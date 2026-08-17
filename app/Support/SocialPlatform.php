<?php

namespace App\Support;

class SocialPlatform
{
    /**
     * Map of recognized domains to a platform key + human-readable label.
     * Add an entry here to teach the system to recognize a new platform's icon.
     *
     * @var array<string, array{key: string, label: string}>
     */
    protected const DOMAIN_MAP = [
        'twitter.com' => ['key' => 'twitter', 'label' => 'Twitter / X'],
        'x.com' => ['key' => 'twitter', 'label' => 'Twitter / X'],
        'instagram.com' => ['key' => 'instagram', 'label' => 'Instagram'],
        'github.com' => ['key' => 'github', 'label' => 'GitHub'],
        'linkedin.com' => ['key' => 'linkedin', 'label' => 'LinkedIn'],
        'facebook.com' => ['key' => 'facebook', 'label' => 'Facebook'],
        'youtube.com' => ['key' => 'youtube', 'label' => 'YouTube'],
        'youtu.be' => ['key' => 'youtube', 'label' => 'YouTube'],
        'tiktok.com' => ['key' => 'tiktok', 'label' => 'TikTok'],
        'threads.net' => ['key' => 'threads', 'label' => 'Threads'],
    ];

    /**
     * Detect the platform for a URL. Falls back to a generic "link" platform
     * for any domain that isn't recognized.
     *
     * @return array{key: string, label: string}
     */
    public static function detect(string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST) ?? '';
        $host = strtolower(preg_replace('/^www\./', '', $host));

        return self::DOMAIN_MAP[$host] ?? ['key' => 'link', 'label' => '連結'];
    }
}

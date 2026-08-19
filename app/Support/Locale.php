<?php

namespace App\Support;

class Locale
{
    /**
     * Map of supported locale codes to their native display label.
     *
     * @var array<string, string>
     */
    protected const SUPPORTED = [
        'zh_TW' => '繁體中文',
        'en' => 'English',
    ];

    public static function supported(): array
    {
        return self::SUPPORTED;
    }

    public static function isSupported(string $locale): bool
    {
        return array_key_exists($locale, self::SUPPORTED);
    }
}

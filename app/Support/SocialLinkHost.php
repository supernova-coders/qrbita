<?php

namespace App\Support;

class SocialLinkHost
{
    /** @var array<string, array<int, string>> */
    private const HOSTS = [
        'facebook' => ['facebook.com', 'fb.me'],
        'instagram' => ['instagram.com'],
        'linkedin' => ['linkedin.com'],
        'whatsapp' => ['whatsapp.com', 'wa.me'],
        'tiktok' => ['tiktok.com'],
        'youtube' => ['youtube.com', 'youtu.be'],
        'x' => ['x.com', 'twitter.com'],
        'github' => ['github.com'],
    ];

    public static function accepts(string $type, string $url): bool
    {
        if (strtolower((string) parse_url($url, PHP_URL_SCHEME)) !== 'https') {
            return false;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        foreach (self::HOSTS[$type] ?? [] as $allowedHost) {
            if ($host === $allowedHost || str_ends_with($host, '.'.$allowedHost)) {
                return true;
            }
        }

        return false;
    }
}

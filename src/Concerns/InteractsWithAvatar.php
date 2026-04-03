<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

trait InteractsWithAvatar
{
    public function findAvatar($value, $ttl = null)
    {
        if (! $value) {
            return null;
        }

        if (Str::isUrl($value)) {
            return $value;
        }

        return Cache::driver('file')->remember("tallkit-avatar-{$value}", $ttl ?? 60 * 60 * 24 * 30, function () use ($value) {
            try {
                $response = Http::timeout(3)
                    ->retry(2, 200)
                    ->get("https://unavatar.io/{$value}?json");

                if (! $response->successful()) {
                    return '';
                }

                $url = $response->json('url');

                if (Str::contains($url, 'fallback', true)) {
                    return '';
                }

                return $url;
            } catch (\Throwable $e) {
                report($e);

                return '';
            }
        });
    }

    public function generateInitials($value, $singleInitials = null)
    {
        $parts = Str::of($value)->title()->ucsplit()->filter();

        if ($parts->isEmpty()) {
            return null;
        }

        if ($singleInitials || ($parts->count() === 1 && strlen($parts->first()) === 1)) {
            return strtoupper($parts[0][0]);
        }

        if ($parts->count() > 1) {
            return strtoupper($parts[0][0].$parts[1][0]);
        }

        return strtoupper($parts[0][0]).strtolower($parts[0][1]);
    }
}

<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use function Illuminate\Support\defer;

trait InteractsWithAvatar
{
    public function avatarUrl($value, $ttl = null): ?string
    {
        if (! $value) {
            return null;
        }

        if (Str::isUrl($value)) {
            return $value;
        }

        // Sends the email/username to unavatar.io, a third party.
        if (! config('tallkit.avatar.unavatar', true)) {
            return null;
        }

        $store = Cache::store();
        // Hashed: an email may hold what a store refuses in a key (Memcached: spaces, over 250 characters).
        $hash = hash('xxh128', (string) $value);
        $key = $this->storageKey('avatar', $hash);
        $cached = $store->get($key);

        if ($cached !== null) {
            return $cached !== '' ? $cached : null;
        }

        if ($store->add($this->storageKey('avatar', 'pending', $hash), true, 60)) {
            defer(function () use ($store, $key, $value, $ttl) {
                [$url, $answered] = $this->avatarLookup($value);

                $store->put($key, $url ?? '', $answered ? ($ttl ?? 60 * 60 * 24 * 30) : 60 * 60);
            });
        }

        return null;
    }

    protected function avatarLookup(string $value): array
    {
        try {
            $response = Http::connectTimeout(2)
                ->timeout(3)
                ->get('https://unavatar.io/'.rawurlencode($value).'?json');
        } catch (\Throwable $e) {
            report($e);

            return [null, false];
        }

        if ($response->notFound()) {
            return [null, true];
        }

        if (! $response->successful()) {
            return [null, false];
        }

        $url = (string) $response->json('url');

        return [$url !== '' && ! Str::contains($url, 'fallback', true) ? $url : null, true];
    }

    public function avatarInitials($value, $singleInitials = null): ?string
    {
        $value = Str::contains((string) $value, '@') ? Str::of($value)->before('@')->replace(['.', '_', '-'], ' ') : $value;
        $parts = Str::of($value)->title()->ucsplit()->map(fn ($part) => trim($part))->filter()->values();

        if ($parts->isEmpty()) {
            return null;
        }

        $first = $parts[0];

        if ($singleInitials || ($parts->count() === 1 && mb_strlen($first) === 1)) {
            return mb_strtoupper(mb_substr($first, 0, 1));
        }

        if ($parts->count() > 1) {
            return mb_strtoupper(mb_substr($first, 0, 1).mb_substr($parts->last(), 0, 1));
        }

        return mb_strtoupper(mb_substr($first, 0, 1)).mb_strtolower(mb_substr($first, 1, 1));
    }
}

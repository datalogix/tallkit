<?php

namespace TALLKit\Concerns;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
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

        $hash = hash('xxh128', (string) $value);
        $avatars = app('tallkit.avatars');

        if ($avatars->offsetExists($hash)) {
            return $avatars[$hash];
        }

        $store = Cache::store();
        $key = $this->storageKey('avatar', $hash);
        $cached = $store->get($key);

        if ($cached !== null) {
            return $avatars[$hash] = $cached !== '' ? $cached : null;
        }

        $avatars[$hash] = null;

        if ($store->add($this->storageKey('avatar', 'pending', $hash), true, 60)) {
            $lookups = app('tallkit.avatar-lookups');

            if (! count($lookups)) {
                defer(fn () => $this->runAvatarLookups($store, $lookups));
            }

            $lookups[$key] = [(string) $value, $ttl];
        }

        return null;
    }

    protected function runAvatarLookups($store, \ArrayObject $lookups): void
    {
        foreach (array_chunk($lookups->getArrayCopy(), 10, true) as $chunk) {
            $responses = Http::pool(fn (Pool $pool) => array_map(
                fn ($key) => $pool->as($key)->connectTimeout(2)->timeout(3)
                    ->get('https://unavatar.io/'.rawurlencode($chunk[$key][0]).'?json'),
                array_keys($chunk),
            ));

            foreach ($chunk as $key => [$value, $ttl]) {
                [$url, $answered] = $this->avatarLookup($responses[$key] ?? null);

                $store->put($key, $url ?? '', $answered ? ($ttl ?? 60 * 60 * 24 * 30) : 60 * 60);
            }
        }

        $lookups->exchangeArray([]);
    }

    protected function avatarLookup(mixed $response): array
    {
        if (! $response instanceof Response) {
            if ($response instanceof \Throwable) {
                report($response);
            }

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

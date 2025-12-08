<?php

namespace TALLKit\Components\Avatar;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use TALLKit\Attributes\Mount;
use TALLKit\Concerns\InteractsWithUser;
use TALLKit\View\BladeComponent;

class Avatar extends BladeComponent
{
    use InteractsWithUser;

    public function __construct(
        public ?string $alt = null,
        public ?string $src = null,
        public ?string $initials = null,
        public string|bool|null $icon = null,
        public string|bool|null $tooltip = null,
        public ?string $size = null,
        public ?bool $square = null,
        public ?string $variant = null,
    ) {}

    protected function mergeCustomAppendedAttributes()
    {
        return [];
    }

    #[Mount()]
    protected function mount()
    {
        $this->initials = $this->generateInitials($this->initials ?? $this->name);
        $this->src ??= $this->findImage($this->email ?? $this->username, $this->attributes->pluck('ttl'));

        if ($this->tooltip === true) {
            $this->tooltip = $this->name ?? false;
        }
    }

    protected function generateInitials($value)
    {
        $parts = Str::of($value)->title()->ucsplit()->filter();

        if ($parts->isEmpty()) {
            return null;
        }

        if ($this->attributes->pluck('initials:single') || ($parts->count() === 1 && strlen($parts->first()) === 1)) {
            return strtoupper($parts[0][0]);
        }

        if ($parts->count() > 1) {
            return strtoupper($parts[0][0].$parts[1][0]);
        }

        return strtoupper($parts[0][0]).strtolower($parts[0][1]);
    }

    public function generateColor()
    {
        $colors = ['red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'];
        $colorSeed = $this->attributes->pluck('color:seed') ?? $this->name ?? $this->icon ?? $this->initials;
        $hash = crc32((string) $colorSeed);

        return $colors[$hash % count($colors)];
    }

    protected function findImage($value, $ttl = null)
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
}

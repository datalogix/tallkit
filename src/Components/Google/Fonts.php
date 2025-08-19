<?php

namespace TALLKit\Components\Google;

use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Fonts extends BladeComponent
{
    public function __construct(
        public null|string|array $families = null,
        public ?string $display = null,
        public bool $prefetch = true,
        public bool $preconnect = true,
        public bool $preload = false,
        public bool $useStylesheet = false,
        public bool $noscript = false,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->url = static::constructUrl($this->families, ! $this->display && $this->preload ? 'swap' : $this->display);
        $this->setVariables('url');
    }

    public static function constructUrl($families, $display = null)
    {
        if (filter_var($families, FILTER_VALIDATE_URL) !== false) {
            return $families;
        }

        $params = collect($families)->map(fn ($family) => 'family='.$family);
        $params->when($display, fn ($collection, $value) => $collection->push('display='.$value));

        return 'https://fonts.googleapis.com/css2?'.$params->join('&');
    }
}

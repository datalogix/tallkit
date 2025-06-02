<?php

namespace TALLKit\Components\Html;

use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Html extends BladeComponent
{
    public function __construct(
        public ?string $lang = null,
        public ?string $title = null,
        public string|false $charset = 'utf-8',
        public string|false $viewport = 'width=device-width, initial-scale=1',
        public bool $csrfToken = true,
        public array $metaTags = [],
        public array $meta = [],
        public string|array $vite = [
            'resources/css/app.css',
            'resources/css/app.scss',
            'resources/css/app.sass',
            'resources/js/app.js',
            'resources/js/app.ts',
        ],
        public string $viteBuildDirectory = 'build',
        public null|string|array $googleFonts = null,
        public null|string|bool $gtag = true,
        public null|string|bool $gtm = true,
        public ?string $typekit = null,
        public array $styles = [],
        public array $scripts = [],
        public string|false $stackStyles = 'styles',
        public string|false $stackScripts = 'scripts',
        public array $components = [],
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->lang ??= app()->getLocale();
        $this->title ??= config('app.name');
        $this->vite = collect($this->vite)->unique()->filter(fn ($path) => file_exists(base_path($path)))->toArray();
        $this->googleFonts = is_string($this->googleFonts) ? ['families' => $this->googleFonts] : $this->googleFonts;
    }
}

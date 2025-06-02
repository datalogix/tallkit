<?php

namespace TALLKit\Components\Html;

use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Meta extends BladeComponent
{
    public function __construct(
        public null|string|false $title = null,
        public null|string|false $description = null,
        public null|string|false $keywords = null,
        public null|string|false $author = null,
        public null|string|false $robots = 'index, follow',
        public null|string|false $type = 'website',
        public null|string|false $card = 'summary_large_image',
        public null|string|false $image = null,
        public null|string|false $url = null,
        public null|string|false $locale = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->title ??= config('app.name');
        $this->description ??= config('app.description');
        $this->keywords ??= config('app.keywords');
        $this->author ??= config('app.author');
        $this->image ??= find_image('meta-image');
        $this->url ??= url()->current();
        $this->locale ??= app()->getLocale();
    }
}

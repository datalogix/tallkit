<?php

namespace TALLKit\Components\Layouts;

use Illuminate\Support\Facades\File;
use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Auth extends BladeComponent
{
    public function __construct(
        public bool $appearance = true,
        public bool $right = false,
        public mixed $bg = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $bgs = collect($this->bg ?? File::glob(public_path('{imgs,images}/{hero/*,heros/*,hero}.{png,jpg,jpeg}'), GLOB_BRACE))
            ->filter()
            ->map(fn ($hero) => asset(str_replace(public_path(), '', $hero)))
            ->unique();

        $this->bg = $bgs->isNotEmpty() ? $bgs->random() : null;
    }
}

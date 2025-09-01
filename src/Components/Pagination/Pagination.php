<?php

namespace TALLKit\Components\Pagination;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\UrlWindow;
use TALLKit\View\BladeComponent;

class Pagination extends BladeComponent
{
    public function __construct(
        public Paginator|CursorPaginator $paginator,
        public bool|string $scrollTo = 'body',
        public ?bool $total = null,
        public ?bool $firstPage = null,
        public ?bool $lastPage = null,
        public ?int $eachSide = null,
    ) {}

    public function elements()
    {
        $this->paginator->onEachSide($this->eachSide ?? 3);

        $window = UrlWindow::make($this->paginator);

        return array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }
}

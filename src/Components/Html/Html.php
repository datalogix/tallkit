<?php

namespace TALLKit\Components\Html;

use TALLKit\View\BladeComponent;

class Html extends BladeComponent
{
    protected function props()
    {
        return [
            'lang' => str_replace('_', '-', app()->getLocale()),
            'title' => config('app.name'),
            'charset' => 'utf-8',
            'viewport' => 'width=device-width, initial-scale=1',
            'csrfToken' => true,
        ];
    }
}

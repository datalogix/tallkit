<?php

namespace TALLKit\Concerns;

use TALLKit\Binders\FormDataBinder;

trait PrepareFormDataBinder
{
    public function startFormDataBinder($bind = null)
    {
        app(FormDataBinder::class)->bind($bind);
    }

    public function endFormDataBinder()
    {
        app(FormDataBinder::class)->endBind();
    }
}

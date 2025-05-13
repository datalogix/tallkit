<?php

namespace TALLKit\Concerns;

use TALLKit\Binders\FormDataBinder;

trait PrepareFormDataBinder
{
    /**
     * Start form data binder.
     *
     * @param  mixed  $bind
     * @return void
     */
    public function startFormDataBinder($bind = null)
    {
        app(FormDataBinder::class)->bind($bind);
    }

    /**
     * End form data binder.
     *
     * @return void
     */
    public function endFormDataBinder()
    {
        app(FormDataBinder::class)->endBind();
    }
}

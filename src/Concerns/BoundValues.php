<?php

namespace TALLKit\Concerns;

use TALLKit\Binders\FormDataBinder;

trait BoundValues
{
    protected function getBoundTarget()
    {
        return app(FormDataBinder::class)->getBind();
    }

    protected function getBoundValue($bind = null, $name = null)
    {
        if ($bind === false) {
            return null;
        }

        $bind ??= $this->getBoundTarget();

        return data_get(value($bind, $name), $name);
    }
}

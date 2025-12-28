<?php

namespace TALLKit\Concerns;

trait InteractsWithElement
{
    use AppendsCustomAttributes;

    protected function customAppendedAttributes()
    {
        return ['icon', 'prefix', 'suffix', 'iconTrailing', 'info', 'badge', 'prepend', 'append', 'kbd'];
    }
}

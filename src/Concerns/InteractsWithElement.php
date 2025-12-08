<?php

namespace TALLKit\Concerns;

trait InteractsWithElement
{
    use AppendsCustomAttributes;

    protected function customAppendedAttributes()
    {
        return ['icon', 'suffix', 'iconTrailing', 'info', 'badge', 'prepend', 'append'];
    }
}

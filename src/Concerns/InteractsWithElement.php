<?php

namespace TALLKit\Concerns;

trait InteractsWithElement
{
    use AppendsCustomAttributes;

    protected function customAppendedAttributes()
    {
        return ['icon', 'suffix', 'iconTrailing', 'information', 'badge', 'prepend', 'append'];
    }
}

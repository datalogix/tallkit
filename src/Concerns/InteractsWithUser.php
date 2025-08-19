<?php

namespace TALLKit\Concerns;

trait InteractsWithUser
{
    use AppendsCustomAttributes;

    protected function customAppendedAttributes()
    {
        return [
            'guard',
            'user' => fn () => auth($this->guard)->user(),
            'name' => fn () => data_get($this->user, 'name'),
            'email' => fn () => data_get($this->user, 'email'),
            'username' => fn () => data_get($this->user, 'username'),
        ];
    }
}

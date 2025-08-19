<?php

namespace TALLKit\View\Concerns;

use Illuminate\Support\Str;
use TALLKit\Attributes\Process;

trait HandlesDataKey
{
    public function baseComponentKey()
    {
        return Str::of($this->componentName)
            ->replace('.', '-')
            ->replace('tallkit-', '')
            ->toString();
    }

    public function buildDataAttribute(string $name)
    {
        return 'data-tallkit-'.Str::kebab($name);
    }

    public function dataKey(?string $suffix = null)
    {
        $base = $this->baseComponentKey();

        return $base
            ? $this->buildDataAttribute($suffix ? "{$base}-".Str::kebab($suffix) : $base)
            : null;
    }

    #[Process()]
    public function injectDataKeyAttribute()
    {
        if ($dataKey = $this->dataKey()) {
            $this->attributes = $this->attributes->merge([$dataKey => '']);
        }
    }
}

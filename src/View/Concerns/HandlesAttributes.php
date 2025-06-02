<?php

namespace TALLKit\View\Concerns;

use Illuminate\Support\Arr;
use ReflectionClass;
use TALLKit\Attributes\Finish;
use TALLKit\Attributes\Mount;
use TALLKit\Attributes\Process;

trait HandlesAttributes
{
    protected array $mount = [];

    protected array $process = [];

    protected array $finish = [];

    protected function bootAttributes()
    {
        $this->mount = $this->getFunctionsByAttribute(Mount::class);
        $this->process = $this->getFunctionsByAttribute(Process::class);
        $this->finish = $this->getFunctionsByAttribute(Finish::class);
    }

    private function getFunctionsByAttribute(string $name)
    {
        $methods = [];

        $reflection = new ReflectionClass($this);

        foreach ($reflection->getMethods() as $method) {
            $attribute = Arr::first($method->getAttributes($name));

            if (! is_null($attribute)) {
                $instance = $attribute->newInstance();

                data_set($methods, $instance->priority, $method->getName());
            }
        }

        return Arr::sort($methods, fn ($value, $key) => $key);
    }
}

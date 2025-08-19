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
        $reflection = new ReflectionClass($this);

        $this->mount = $this->getFunctionsByAttribute($reflection, Mount::class);
        $this->process = $this->getFunctionsByAttribute($reflection, Process::class);
        $this->finish = $this->getFunctionsByAttribute($reflection, Finish::class);
    }

    private function getFunctionsByAttribute(ReflectionClass $reflection, string $name)
    {
        $methods = [];

        foreach ($reflection->getMethods() as $method) {
            $attribute = Arr::first($method->getAttributes($name));

            if ($attribute) {
                $instance = $attribute->newInstance();
                $methods[$instance->priority][] = $method->getName();
            }
        }

        ksort($methods);

        return array_merge(...array_values($methods));
    }
}

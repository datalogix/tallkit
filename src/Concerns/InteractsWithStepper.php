<?php

namespace TALLKit\Concerns;

use Illuminate\View\Factory;

trait InteractsWithStepper
{
    // Steps render before the stepper's view, which resets the count; per component depth for nested steppers.
    public function stepperNextPosition(): int
    {
        $counts = app('tallkit.ids');
        $key = 'stepper-step|'.$this->viewComponentDepth();

        return $counts[$key] = ($counts[$key] ?? 0) + 1;
    }

    public function stepperReset(): void
    {
        unset(app('tallkit.ids')['stepper-step|'.($this->viewComponentDepth() + 1)]);
    }

    // Blade exposes no public component depth.
    protected function viewComponentDepth(): int
    {
        static $stack;

        $stack ??= new \ReflectionProperty(Factory::class, 'componentStack');

        return count($stack->getValue(app('view')));
    }
}

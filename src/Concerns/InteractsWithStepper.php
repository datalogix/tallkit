<?php

namespace TALLKit\Concerns;

use Illuminate\View\Factory;

trait InteractsWithStepper
{
    public function stepperNextPosition(): int
    {
        $counts = app('tallkit.ids');
        $steppers = array_filter($this->viewComponentStack(), fn ($view) => is_string($view) && str_ends_with($view, '::stepper.stepper'));
        $key = 'stepper-step|'.array_key_last($steppers);

        return $counts[$key] = ($counts[$key] ?? 0) + 1;
    }

    public function stepperReset(): void
    {
        unset(app('tallkit.ids')['stepper-step|'.count($this->viewComponentStack())]);
    }

    protected function viewComponentStack(): array
    {
        static $stack;

        $stack ??= new \ReflectionProperty(Factory::class, 'componentStack');

        return $stack->getValue(app('view'));
    }
}

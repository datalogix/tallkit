<?php

namespace TALLKit\Tests\Unit\Concerns;

use Illuminate\Support\Facades\Blade;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithStepperTest extends TestCase
{
    protected function statuses(string $html): array
    {
        preg_match_all('/data-status="([a-z]+)"/', $html, $matches);

        return $matches[1];
    }

    public function test_next_position_outside_a_stepper_counts_on_its_own_key(): void
    {
        $this->assertSame(1, TALLKit::stepperNextPosition());
        $this->assertSame(2, TALLKit::stepperNextPosition());
        $this->assertSame(2, app('tallkit.ids')['stepper-step|']);
    }

    public function test_next_position_is_keyed_by_the_stepper_position_in_the_component_stack(): void
    {
        $view = app('view');
        $view->startComponent('outer');
        $view->startComponent('tallkit::stepper.stepper');
        $view->startComponent('step');

        try {
            $this->assertSame(1, TALLKit::stepperNextPosition());
            $this->assertSame(2, TALLKit::stepperNextPosition());
            $this->assertSame(2, app('tallkit.ids')['stepper-step|1']);
        } finally {
            ob_end_clean();
            ob_end_clean();
            ob_end_clean();
            $view->flushState();
        }

        app('view')->startComponent('outer');

        try {
            TALLKit::stepperReset();
        } finally {
            ob_end_clean();
            $view->flushState();
        }

        $this->assertFalse(isset(app('tallkit.ids')['stepper-step|1']));
    }

    public function test_reset_only_clears_the_current_level(): void
    {
        app('tallkit.ids')['stepper-step|0'] = 3;
        app('tallkit.ids')['stepper-step|1'] = 2;

        TALLKit::stepperReset();

        $this->assertSame(['stepper-step|1' => 2], app('tallkit.ids')->getArrayCopy());
    }

    public function test_steps_without_index_get_status_from_their_position(): void
    {
        $template = '<tk:stepper current="2"><tk:stepper.step label="A"/><tk:stepper.step label="B"/><tk:stepper.step label="C"/></tk:stepper>';

        $this->assertSame(['completed', 'active', 'pending'], $this->statuses(Blade::render($template)));
        $this->assertSame([], app('tallkit.ids')->getArrayCopy());
        $this->assertSame(
            ['completed', 'active', 'pending', 'completed', 'active', 'pending'],
            $this->statuses(Blade::render($template.$template)),
        );
    }

    public function test_nested_steppers_count_separately(): void
    {
        $html = Blade::render(<<<'BLADE'
            <tk:stepper current="1">
                <tk:stepper.step label="A"/>
                <tk:stepper current="2">
                    <tk:stepper.step label="X"/>
                    <tk:stepper.step label="Y"/>
                </tk:stepper>
                <tk:stepper.step label="B"/>
            </tk:stepper>
        BLADE);

        $this->assertSame(['active', 'completed', 'active', 'pending'], $this->statuses($html));
    }
}

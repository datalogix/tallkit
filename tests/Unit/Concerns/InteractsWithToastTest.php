<?php

namespace TALLKit\Tests\Unit\Concerns;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Livewire\Livewire;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithToastTest extends TestCase
{
    protected function flashed(): array
    {
        return Session::get('tallkit.toasts', []);
    }

    public function test_toast_without_session_is_ignored(): void
    {
        $this->assertNull(TALLKit::toast('Saved'));
        $this->assertSame([], TALLKit::toastPullFlashed());
        $this->assertFalse($this->app['session']->isStarted());
    }

    public function test_toast_is_flashed_outside_livewire(): void
    {
        $this->startSession();

        $this->assertNull(TALLKit::toast('Saved', 'Done', 'success', 3000, 'top-end', true, 'sm', false, [['label' => 'Undo']], 'toast-1'));
        TALLKit::toast(title: 'Only title');

        $this->assertSame([
            [
                'message' => 'Saved',
                'title' => 'Done',
                'type' => 'success',
                'duration' => 3000,
                'position' => 'top-end',
                'progress' => true,
                'size' => 'sm',
                'invert' => false,
                'actions' => [['label' => 'Undo']],
                'id' => 'toast-1',
            ],
            ['title' => 'Only title'],
        ], $this->flashed());
    }

    public function test_htmlable_content_escapes_the_plain_part(): void
    {
        $this->startSession();

        TALLKit::toast(new HtmlString('<b>Saved</b>'), '<i>Title</i>');
        TALLKit::toast('<b>Raw</b>');

        $this->assertSame([
            ['message' => '<b>Saved</b>', 'title' => '&lt;i&gt;Title&lt;/i&gt;', 'html' => true],
            ['message' => '<b>Raw</b>'],
        ], $this->flashed());
    }

    public function test_htmlable_title_with_null_message(): void
    {
        $this->startSession();

        TALLKit::toast(null, new HtmlString('<b>T</b>'));

        $this->assertSame([['title' => '<b>T</b>', 'html' => true]], $this->flashed());
    }

    public function test_type_proxy_maps_positional_arguments(): void
    {
        $this->startSession();

        TALLKit::toast()->success('Saved', 'Done', 5000);
        TALLKit::toast()->danger('Failed', position: 'bottom');

        $this->assertSame([
            ['message' => 'Saved', 'title' => 'Done', 'type' => 'success', 'duration' => 5000],
            ['message' => 'Failed', 'type' => 'danger', 'position' => 'bottom'],
        ], $this->flashed());
    }

    public function test_type_proxy_close_outside_livewire_does_nothing(): void
    {
        $this->assertNull(TALLKit::toast()->close('toast-1'));
    }

    public function test_pull_flashed(): void
    {
        $this->startSession();

        TALLKit::flashToast(['message' => 'A']);
        TALLKit::flashToast(['message' => 'B']);

        $this->assertSame([['message' => 'A'], ['message' => 'B']], TALLKit::toastPullFlashed());
        $this->assertSame([], TALLKit::toastPullFlashed());
    }

    public function test_toast_inside_livewire_runs_js(): void
    {
        $component = Livewire::test(new class extends Component
        {
            public function notify()
            {
                TALLKit::toast(new HtmlString('<b>Saved</b>'), type: 'success', actions: [
                    ['label' => 'Undo', 'method' => 'undo'],
                    ['label' => 'Other', 'method' => 'go', 'component' => 'other-id'],
                    ['label' => 'Link', 'href' => '/x'],
                ]);
            }

            public function plain()
            {
                TALLKit::toast('Hi');
            }

            public function close()
            {
                TALLKit::toast()->close('toast-1');
            }

            public function render()
            {
                return '<div></div>';
            }
        });

        $component->call('notify')->assertJs('$tallkit.toast', [
            'message' => '<b>Saved</b>',
            'type' => 'success',
            'actions' => [
                ['label' => 'Undo', 'method' => 'undo', 'component' => $component->id()],
                ['label' => 'Other', 'method' => 'go', 'component' => 'other-id'],
                ['label' => 'Link', 'href' => '/x'],
            ],
            'html' => true,
        ]);

        $component->call('plain')->assertJs('$tallkit.toast', ['message' => 'Hi']);
        $component->call('close')->assertJs('$tallkit.toast().close', 'toast-1');
    }
}

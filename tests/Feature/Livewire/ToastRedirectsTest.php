<?php

namespace TALLKit\Tests\Feature\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Livewire;
use TALLKit\Tests\TestCase;

class ToastRedirectsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->startSession();
    }

    protected function livewire()
    {
        return Livewire::test(new class extends Component
        {
            public function save()
            {
                $this->js('console.log', 1);
                $this->toast('Saved', 'Done', actions: [
                    ['label' => 'Undo', 'method' => 'undo'],
                    ['label' => 'Open', 'href' => '/open'],
                ]);
                $this->redirect('/next');
            }

            public function positional()
            {
                $this->js('$tallkit.toast', 'Saved', 'Done', 'success', null, null, null, null, null, [], 'id-1', 'extra');
                $this->js('$tallkit.toast', ['Saved']);
                $this->redirect('/next');
            }

            public function onlyMethodActions()
            {
                $this->js('$tallkit.toast', ['message' => 'Hi', 'actions' => [['label' => 'Undo', 'method' => 'undo']]]);
                $this->redirect('/next');
            }

            public function withoutToast()
            {
                $this->js('console.log', 1);
                $this->redirect('/next');
            }

            public function withoutRedirect()
            {
                $this->toast('Saved');
            }

            public function render()
            {
                return '<div></div>';
            }
        });
    }

    public function test_toasts_before_a_redirect_are_flashed(): void
    {
        $component = $this->livewire()->call('save')->assertRedirect('/next');

        $this->assertSame([['message' => 'Saved', 'title' => 'Done', 'actions' => [['label' => 'Open', 'href' => '/open']]]], Session::get('tallkit.toasts'));
        $this->assertSame([['expression' => 'console.log', 'params' => [1]]], $component->effects['xjs']);
    }

    public function test_positional_params_are_named(): void
    {
        $this->livewire()->call('positional');

        $this->assertSame([
            ['message' => 'Saved', 'title' => 'Done', 'type' => 'success', 'id' => 'id-1'],
            ['message' => ['Saved']],
        ], Session::get('tallkit.toasts'));
    }

    public function test_method_actions_are_dropped(): void
    {
        $this->livewire()->call('onlyMethodActions');

        $this->assertSame([['message' => 'Hi']], Session::get('tallkit.toasts'));
    }

    public function test_redirect_without_toasts_keeps_js(): void
    {
        $component = $this->livewire()->call('withoutToast');

        $this->assertNull(Session::get('tallkit.toasts'));
        $this->assertSame([['expression' => 'console.log', 'params' => [1]]], $component->effects['xjs']);
    }

    public function test_toast_without_redirect_stays_in_js(): void
    {
        $this->livewire()->call('withoutRedirect')->assertJs('$tallkit.toast', ['message' => 'Saved']);

        $this->assertNull(Session::get('tallkit.toasts'));
    }
}

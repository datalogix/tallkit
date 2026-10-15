<?php

namespace TALLKit\Tests\Unit\Concerns;

use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Livewire;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithModalTest extends TestCase
{
    public function test_show_outside_livewire_flashes_the_modal(): void
    {
        $this->startSession();

        TALLKit::modal('edit')->show();
        TALLKit::modal('edit')->show();
        TALLKit::modal('delete')->show();

        $this->assertSame(['edit', 'delete'], Session::get('tallkit.modals'));
        $this->assertTrue(TALLKit::modalFlashed('edit'));
        $this->assertFalse(TALLKit::modalFlashed('other'));
        $this->assertFalse(TALLKit::modalFlashed(null));
    }

    public function test_without_session_nothing_is_flashed(): void
    {
        TALLKit::modal('edit')->show();
        TALLKit::flashModal('edit');

        $this->assertFalse(TALLKit::modalFlashed('edit'));
        $this->assertFalse($this->app['session']->isStarted());
    }

    public function test_close_outside_livewire_does_nothing(): void
    {
        $this->startSession();

        TALLKit::modal('edit')->close();
        TALLKit::modals()->close();

        $this->assertNull(Session::get('tallkit.modals'));
    }

    public function test_inside_livewire_dispatches_events(): void
    {
        $component = Livewire::test(new class extends Component
        {
            public function showModal()
            {
                TALLKit::modal('edit')->show();
            }

            public function showScoped()
            {
                TALLKit::modal('edit', scope: true)->show();
            }

            public function closeModal()
            {
                TALLKit::modal('edit')->close();
            }

            public function closeScoped()
            {
                TALLKit::modal('edit', true)->close();
            }

            public function closeAll()
            {
                TALLKit::modals()->close();
            }

            public function render()
            {
                return '<div></div>';
            }
        });

        $id = $component->id();
        $dispatch = fn (string $method) => $component->call($method)->effects['dispatches'];

        $this->assertSame([['name' => 'tallkit:modal-show', 'params' => ['name' => 'edit', 'scope' => null]]], $dispatch('showModal'));
        $this->assertSame([['name' => 'tallkit:modal-show', 'params' => ['name' => 'edit', 'scope' => $id]]], $dispatch('showScoped'));
        $this->assertSame([['name' => 'tallkit:modal-close', 'params' => ['name' => 'edit', 'scope' => null]]], $dispatch('closeModal'));
        $this->assertSame([['name' => 'tallkit:modal-close', 'params' => ['name' => 'edit', 'scope' => $id]]], $dispatch('closeScoped'));
        $this->assertSame([['name' => 'tallkit:modal-close', 'params' => []]], $dispatch('closeAll'));
    }
}

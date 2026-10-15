<?php

namespace TALLKit\Tests\Feature\Livewire;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Livewire;
use TALLKit\Tests\TestCase;

class ComponentMixinsTest extends TestCase
{
    protected function makeComponent(): Component
    {
        return new class extends Component
        {
            public function render()
            {
                return '<div></div>';
            }
        };
    }

    public function test_macros_are_registered(): void
    {
        foreach (['alert', 'modal', 'modals', 'toast', 'uploadPrune', 'uploadKept', 'notificationMarkAsRead', 'notificationMarkAllAsRead', 'notificationDelete', 'notificationUser'] as $macro) {
            $this->assertTrue(Component::hasMacro($macro), $macro);
        }
    }

    public function test_alert(): void
    {
        $this->startSession();

        $this->assertNull($this->makeComponent()->alert('Saved', 'success'));

        $this->assertSame('Saved', Session::get('status.message'));
        $this->assertSame('success', Session::get('status.type'));
    }

    public function test_toast_and_modal_inside_livewire(): void
    {
        $component = Livewire::test(new class extends Component
        {
            public function notify()
            {
                $this->toast('Saved', type: 'success');
            }

            public function open()
            {
                $this->modal('edit')->show();
            }

            public function closeAll()
            {
                $this->modals()->close();
            }

            public function render()
            {
                return '<div></div>';
            }
        });

        $component->call('notify')->assertJs('$tallkit.toast', ['message' => 'Saved', 'type' => 'success']);

        $this->assertSame(
            [['name' => 'tallkit:modal-show', 'params' => ['name' => 'edit', 'scope' => $component->id()]]],
            $component->call('open')->effects['dispatches'],
        );
        $this->assertSame([['name' => 'tallkit:modal-close', 'params' => []]], $component->call('closeAll')->effects['dispatches']);
    }

    public function test_upload_prune_and_kept(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('tallkit-uploads/a.jpg', 'a');
        Storage::disk('public')->put('tallkit-uploads/b.jpg', 'b');

        $component = $this->makeComponent();

        $this->assertSame(
            ['tallkit-uploads/a.jpg'],
            $component->uploadKept(['tallkit-uploads/a.jpg'], ['tallkit-uploads/a.jpg', 'other.jpg']),
        );
        $this->assertSame(
            ['tallkit-uploads/b.jpg'],
            $component->uploadPrune(['tallkit-uploads/a.jpg', 'tallkit-uploads/b.jpg'], ['tallkit-uploads/a.jpg'], 'public'),
        );

        Storage::disk('public')->assertExists('tallkit-uploads/a.jpg');
        Storage::disk('public')->assertMissing('tallkit-uploads/b.jpg');
    }
}

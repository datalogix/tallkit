<?php

namespace TALLKit\Tests\Unit;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\View\ComponentSlot;
use Livewire\Component;
use Livewire\Livewire;
use TALLKit\Tests\TestCase;

class HelpersTest extends TestCase
{
    public function test_helpers_do_not_redeclare_existing_functions(): void
    {
        require __DIR__.'/../../src/helpers.php';

        $this->assertTrue(function_exists('route_detect'));
        $this->assertTrue(function_exists('alert'));
    }

    public function test_route_detect(): void
    {
        Route::get('/users/{id}', fn () => '')->name('users.show');
        Route::getRoutes()->refreshNameLookups();

        $this->assertSame('http://localhost/users/5', route_detect(['missing', 'users.show'], ['id' => 5]));
        $this->assertSame('/', route_detect('missing'));
        $this->assertSame('/home', route_detect(null, default: '/home'));
        $this->assertNull(route_detect(['missing'], default: null));
    }

    public function test_has_content(): void
    {
        $this->assertFalse(has_content());
        $this->assertFalse(has_content(null, '', false, new ComponentSlot('')));
        $this->assertTrue(has_content(null, 0));
        $this->assertTrue(has_content(new ComponentSlot('x')));
    }

    public function test_string_value(): void
    {
        $this->assertSame('a', string_value('a'));
        $this->assertNull(string_value(''));
        $this->assertSame('d', string_value('', 'd'));
        $this->assertSame('d', string_value(1, 'd'));
        $this->assertNull(string_value(['a']));
    }

    public function test_make_model(): void
    {
        $this->assertInstanceOf(HelperModel::class, make_model(HelperModel::class));

        if (! class_exists('App\Models\TallkitHelperWidget')) {
            class_alias(HelperModel::class, 'App\Models\TallkitHelperWidget');
        }

        $this->assertInstanceOf(HelperModel::class, make_model('tallkit_helper_widget'));
        $this->assertNull(make_model('tallkit_missing_model'));
    }

    public function test_in_livewire_and_unless_livewire(): void
    {
        $this->assertFalse(in_livewire());
        $this->assertSame('value', unless_livewire('value'));

        Livewire::test(new class extends Component
        {
            public function render()
            {
                return '<div>{{ var_export(in_livewire(), true) }}|{{ var_export(unless_livewire("value"), true) }}</div>';
            }
        })->assertSee('true|NULL');
    }

    public function test_toast_and_alert_helpers(): void
    {
        $this->startSession();

        $this->assertNull(toast('Saved'));
        $this->assertIsObject(toast());
        $this->assertNull(alert('Done', 'success'));
        $this->assertIsObject(alert());

        $this->assertSame([['message' => 'Saved']], Session::get('tallkit.toasts'));
        $this->assertSame('Done', Session::get('status.message'));
        $this->assertSame('success', Session::get('status.type'));
    }
}

class HelperModel {}

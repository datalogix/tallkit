<?php

namespace TALLKit\Tests\Unit\Concerns;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Form;
use Livewire\Livewire;
use TALLKit\Facades\TALLKit;
use TALLKit\TALLKit as TALLKitClass;
use TALLKit\Tests\TestCase;

enum InteractsWithLivewireStatus: string
{
    case Active = 'active';
}

class InteractsWithLivewireForm extends Form
{
    public ?Carbon $date = null;
}

class InteractsWithLivewireProbe extends Component
{
    public static ?string $referer = null;

    public InteractsWithLivewireForm $form;

    public ?Carbon $date = null;

    public string|Carbon|null $when = null;

    public int $count = 0;

    public $untyped;

    #[Url]
    public string $search = 'bob';

    #[Url(as: 'st')]
    public ?InteractsWithLivewireStatus $status = InteractsWithLivewireStatus::Active;

    #[Url]
    public array $tags = [];

    #[Url]
    public string $uninitialized;

    public string $plain = 'plain';

    public function render()
    {
        if (static::$referer !== null) {
            request()->headers->set('referer', static::$referer);
        }

        return <<<'BLADE'
        <div>@php(\TALLKit\Tests\Unit\Concerns\InteractsWithLivewireTest::capture())</div>
        BLADE;
    }
}

class InteractsWithLivewireTest extends TestCase
{
    public static array $livewireCaptured = [];

    protected function setUp(): void
    {
        parent::setUp();

        app('livewire')->flushState();
        static::$livewireCaptured = [];
        InteractsWithLivewireProbe::$referer = null;
    }

    public static function capture(): void
    {
        static::$livewireCaptured[] = [
            'rendering' => TALLKit::livewireRendering(),
            'component' => TALLKit::livewireComponent()?->getId(),
            'livewire-request' => request()->hasHeader('X-Livewire'),
            'sort-state' => TALLKit::tableSortState(),
            'sort-href' => TALLKit::tableSortHref('id', 'asc'),
            'is' => [
                'date' => TALLKit::livewirePropertyIs('date', \DateTimeInterface::class),
                'when' => TALLKit::livewirePropertyIs('when', \DateTimeInterface::class),
                'count' => TALLKit::livewirePropertyIs('count', \DateTimeInterface::class),
                'untyped' => TALLKit::livewirePropertyIs('untyped', \DateTimeInterface::class),
                'missing' => TALLKit::livewirePropertyIs('missing', \DateTimeInterface::class),
                'form.date' => TALLKit::livewirePropertyIs('form.date', \DateTimeInterface::class),
                'form.missing' => TALLKit::livewirePropertyIs('form.missing', \DateTimeInterface::class),
                'nothing.date' => TALLKit::livewirePropertyIs('nothing.date', \DateTimeInterface::class),
            ],
        ];
    }

    protected function setInstalled(?bool $installed): void
    {
        (new \ReflectionProperty(TALLKitClass::class, 'livewireInstalled'))->setValue(null, $installed);
    }

    public function test_outside_a_component(): void
    {
        $this->assertTrue(TALLKit::livewireInstalled());
        $this->assertFalse(TALLKit::livewireRendering());
        $this->assertNull(TALLKit::livewireComponent());
        $this->assertFalse(TALLKit::livewireComponentRendered());
        $this->assertFalse(TALLKit::livewirePropertyIs('date', \DateTimeInterface::class));
    }

    public function test_inside_a_component(): void
    {
        $component = Livewire::test(InteractsWithLivewireProbe::class);
        $captured = static::$livewireCaptured[0];

        $this->assertTrue($captured['rendering']);
        $this->assertSame($component->id(), $captured['component']);
        $this->assertSame([
            'date' => true,
            'when' => true,
            'count' => false,
            'untyped' => false,
            'missing' => false,
            'form.date' => true,
            'form.missing' => false,
            'nothing.date' => false,
        ], $captured['is']);
        $this->assertFalse(TALLKit::livewireRendering());
    }

    public function test_component_rendered(): void
    {
        $this->assertFalse(TALLKit::livewireComponentRendered());

        Livewire::component('interacts-with-livewire-probe', InteractsWithLivewireProbe::class);
        Blade::render('<livewire:interacts-with-livewire-probe />');

        $this->assertTrue(TALLKit::livewireComponentRendered());
    }

    public function test_query_of_the_first_render_comes_from_the_request(): void
    {
        $component = Livewire::withQueryParams(['sortBy' => 'name', 'sortDirection' => 'desc', 'page' => 2])
            ->test(InteractsWithLivewireProbe::class);
        $captured = static::$livewireCaptured[0];

        $this->assertFalse($captured['livewire-request']);
        $this->assertSame(['name', 'desc'], $captured['sort-state']);
        $this->assertStringEndsWith('?sortBy=id&sortDirection=asc', $captured['sort-href']);
        $this->assertStringNotContainsString('page=', $captured['sort-href']);
    }

    public function test_query_of_a_livewire_update_comes_from_the_referer_and_url_properties(): void
    {
        InteractsWithLivewireProbe::$referer = 'http://localhost/users?sortBy=name&sortDirection=desc&tags=old&st=old&page=4&keep=1';

        $component = Livewire::test(InteractsWithLivewireProbe::class)->call('$refresh');
        $captured = end(static::$livewireCaptured);

        $this->assertTrue($captured['livewire-request']);
        $this->assertSame(['name', 'desc'], $captured['sort-state']);
        $this->assertSame(
            url($component->snapshot['memo']['path']).'?sortBy=id&sortDirection=asc&st=active&keep=1&search=bob',
            $captured['sort-href'],
        );
    }

    public function test_query_of_a_livewire_request_without_component(): void
    {
        $this->app->instance('request', Request::create(
            '/livewire/update',
            'POST',
            ['components' => [['snapshot' => json_encode(['memo' => ['path' => 'users']])]]],
            server: ['HTTP_X_LIVEWIRE' => '1', 'HTTP_REFERER' => 'http://localhost/users?q=a&sortBy=email&page=2'],
        ));

        $this->assertSame(['email', 'asc'], TALLKit::tableSortState());
        $this->assertSame('http://localhost/users?q=a&sortBy=name&sortDirection=desc', TALLKit::tableSortHref('name', 'desc'));
    }

    public function test_live_model(): void
    {
        $this->assertSame('search', TALLKit::livewireLiveModel(new ComponentAttributeBag(['wire:model.live.debounce' => 'search'])));
        $this->assertNull(TALLKit::livewireLiveModel(new ComponentAttributeBag(['wire:model' => 'search'])));
        $this->assertNull(TALLKit::livewireLiveModel(new ComponentAttributeBag(['wire:model.blur' => 'search'])));
        $this->assertNull(TALLKit::livewireLiveModel(new ComponentAttributeBag));
    }

    public function test_without_livewire_installed(): void
    {
        $this->setInstalled(false);

        try {
            $this->app->instance('request', Request::create('/users?sortBy=name', server: ['HTTP_X_LIVEWIRE' => '1']));

            $this->assertFalse(TALLKit::livewireInstalled());
            $this->assertFalse(TALLKit::livewireRendering());
            $this->assertNull(TALLKit::livewireComponent());
            $this->assertFalse(TALLKit::livewireComponentRendered());
            $this->assertNull(TALLKit::livewireLiveModel(new ComponentAttributeBag(['wire:model.live' => 'search'])));
            $this->assertSame(['name', 'asc'], TALLKit::tableSortState());
            $this->assertSame('http://localhost/users?sortBy=id&sortDirection=asc', TALLKit::tableSortHref('id', 'asc'));
        } finally {
            $this->setInstalled(null);
        }
    }
}

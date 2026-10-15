<?php

namespace TALLKit\Tests\Feature\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use TALLKit\Livewire\ConfirmsSimilar;
use TALLKit\Livewire\Similar;
use TALLKit\Tests\TestCase;

class ConfirmsSimilarTest extends TestCase
{
    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        $app['config']->set('database.default', 'testing');
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('similar_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
        });

        foreach (['Camisa polo', 'TV', 'Atividades', 'Calçados', 'Bebidas'] as $name) {
            SimilarCategory::create(['name' => $name, 'color' => 'red']);
        }
    }

    protected function queryComponent(): Component
    {

        return new class extends Component
        {
            use ConfirmsSimilar;

            #[Similar]
            public string $name = '';

            public ?int $ignore = null;

            public int $threshold = 75;

            public int $minLength = 3;

            protected function findSimilar(): iterable
            {
                return $this->querySimilar(
                    SimilarCategory::query()->when($this->ignore, fn ($q, $id) => $q->whereKeyNot($id)),
                    threshold: $this->threshold,
                    minLength: $this->minLength,
                );
            }

            protected function storeSimilar() {}

            public function render()
            {
                return '<div></div>';
            }
        };
    }

    protected function editComponent(): Component
    {
        return new class extends Component
        {
            use ConfirmsSimilar;

            public SimilarCategory $category;

            #[Similar(record: 'category')]
            public string $name = '';

            public int $saves = 0;

            public function mount(SimilarCategory $category): void
            {
                $this->category = $category;
                $this->name = $category->name ?? '';
            }

            protected function findSimilar(): iterable
            {
                return $this->querySimilar(SimilarCategory::query());
            }

            protected function storeSimilar()
            {
                $this->category->update(['name' => $this->name]);
                $this->saves++;
            }

            public function render()
            {
                return '<div><tk:similar /></div>';
            }
        };
    }

    public function test_editing_skips_the_record_itself_and_says_save(): void
    {
        Livewire::test($this->editComponent(), ['category' => SimilarCategory::find(5)])
            ->set('name', 'Bebidaz')
            ->call('saveSimilar')
            ->assertSet('similar', [])
            ->assertSet('saves', 1);

        Livewire::test($this->editComponent(), ['category' => SimilarCategory::find(5)])
            ->set('name', 'Camisa')
            ->call('saveSimilar')
            ->assertSet('similar', [['id' => 1, 'name' => 'Camisa polo']])
            ->assertSee('Do you want to save &quot;Camisa&quot; anyway?', escape: false)
            ->assertSee('Save anyway')
            ->assertDontSee('Create anyway')
            ->call('confirmSimilar')
            ->assertSet('saves', 1);

        $this->assertSame('Camisa', SimilarCategory::find(5)->name);
    }

    public function test_editing_without_changing_the_value_stores_directly(): void
    {
        $component = Livewire::test($this->editComponent(), ['category' => SimilarCategory::find(1)]);
        SimilarCategory::create(['name' => 'Camisa polo azul']);

        $component->call('saveSimilar')->assertSet('similar', [])->assertSet('saves', 1);
        $component->set('name', ' Camisa   polo ')->call('saveSimilar')->assertSet('similar', [])->assertSet('saves', 2);
        $component->set('name', 'Camisa polos')->call('saveSimilar')->assertSet('similar', [['id' => 6, 'name' => 'Camisa polo azul']]);
    }

    public function test_a_new_record_behaves_like_creating(): void
    {
        Livewire::test($this->editComponent(), ['category' => new SimilarCategory])
            ->set('name', 'Bebida')
            ->call('saveSimilar')
            ->assertSet('similar', [['id' => 5, 'name' => 'Bebidas']])
            ->assertSee('Create anyway');
    }

    public function test_query_similar_matches_containment_and_close_spellings(): void
    {
        $component = Livewire::test($this->queryComponent());

        $component->set('name', 'camisa')->call('saveSimilar')->assertSet('similar', [['id' => 1, 'name' => 'Camisa polo']]);
        $component->set('name', 'Calcados')->call('saveSimilar')->assertSet('similar', [['id' => 4, 'name' => 'Calçados']]);
        $component->set('name', 'Bebida')->call('saveSimilar')->assertSet('similar', [['id' => 5, 'name' => 'Bebidas']]);
        $component->set('name', 'Livros')->call('saveSimilar')->assertSet('similar', []);
    }

    public function test_query_similar_needs_three_characters_to_match_by_containment(): void
    {
        $component = Livewire::test($this->queryComponent());

        $component->set('name', 'Atividades escolares')->call('saveSimilar')->assertSet('similar', [['id' => 3, 'name' => 'Atividades']]);
        $component->set('name', 'tv')->call('saveSimilar')->assertSet('similar', [['id' => 2, 'name' => 'TV']]);
        $component->set('name', 'iv')->call('saveSimilar')->assertSet('similar', []);
        $component->set('minLength', 2)->call('saveSimilar')->assertSet('similar', [['id' => 3, 'name' => 'Atividades']]);
    }

    public function test_query_similar_ignores_an_empty_slug(): void
    {
        Livewire::test($this->queryComponent())->set('name', '!!!')->call('saveSimilar')->assertSet('similar', []);
    }

    public function test_query_similar_respects_the_query_and_threshold(): void
    {
        $component = Livewire::test($this->queryComponent());

        $component->set('ignore', 5)->set('name', 'Bebida')->call('saveSimilar')->assertSet('similar', []);
        $component->set('ignore', null)->set('name', 'Bebidaz')->call('saveSimilar')->assertSet('similar', [['id' => 5, 'name' => 'Bebidas']]);
        $component->set('threshold', 90)->set('name', 'Bebidaz')->call('saveSimilar')->assertSet('similar', []);
    }

    protected function makeComponent(): Component
    {
        return new class extends Component
        {
            use ConfirmsSimilar;

            #[Similar]
            public string $name = '';

            public bool $other = false;

            public bool $race = false;

            public array $existing = ['Foods', 'Drinks'];

            public array $stored = [];

            protected function rules(): array
            {
                return ['name' => 'required'];
            }

            protected function findSimilar(): iterable
            {
                return collect($this->existing)
                    ->filter(fn ($name) => similar_text(strtolower($name), strtolower($this->name)) >= 4)
                    ->map(fn ($name, $id) => ['id' => $id, 'name' => $name]);
            }

            protected function storeSimilar()
            {
                if ($this->race) {
                    throw new UniqueConstraintViolationException('sqlite', 'insert', [], new \Exception);
                }

                $this->stored[] = $this->name;
                $this->reset('name');
            }

            public function render()
            {
                return '<div><tk:similar /></div>';
            }
        };
    }

    public function test_stores_when_nothing_is_similar(): void
    {
        Livewire::test($this->makeComponent())
            ->set('name', 'Books')
            ->call('saveSimilar')
            ->assertSet('similar', [])
            ->assertSet('stored', ['Books']);
    }

    public function test_waits_for_confirmation_when_something_is_similar(): void
    {
        Livewire::test($this->makeComponent())
            ->set('name', 'Food')
            ->call('saveSimilar')
            ->assertSet('similar', [['id' => 0, 'name' => 'Foods']])
            ->assertSet('stored', [])
            ->assertSee('Foods')
            ->assertSee('Do you want to create &quot;Food&quot; anyway?', escape: false)
            ->call('confirmSimilar')
            ->assertSet('similar', [])
            ->assertSet('stored', ['Food']);
    }

    public function test_cancel_and_editing_the_similar_field_clear_the_list(): void
    {
        $component = Livewire::test($this->makeComponent())->set('name', 'Food')->call('saveSimilar');

        $component->call('cancelSimilar')->assertSet('similar', [])->assertSet('stored', []);

        $component->call('saveSimilar')->set('other', true)->assertNotSet('similar', []);
        $component->set('name', 'Foo')->assertSet('similar', []);
    }

    public function test_confirming_after_editing_the_similar_field_checks_again(): void
    {
        Livewire::test($this->makeComponent())
            ->set('name', 'Food')
            ->call('saveSimilar')
            ->update(calls: [['method' => 'confirmSimilar', 'params' => [], 'path' => '']], updates: ['name' => 'Drinkz'])
            ->assertSet('similar', [['id' => 1, 'name' => 'Drinks']])
            ->assertSet('stored', []);
    }

    public function test_editing_the_similar_field_clears_its_error(): void
    {
        Livewire::test($this->makeComponent())
            ->call('saveSimilar')
            ->assertHasErrors('name')
            ->set('name', 'F')
            ->assertHasNoErrors('name');
    }

    public function test_squishes_the_similar_field_before_validating(): void
    {
        Livewire::test($this->makeComponent())
            ->set('name', "  Books \n  club ")
            ->call('saveSimilar')
            ->assertSet('stored', ['Books club']);
    }

    public function test_similar_is_locked(): void
    {
        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::test($this->makeComponent())->set('similar', [['id' => 9, 'name' => 'X']]);
    }

    public function test_unique_violation_becomes_an_error_on_the_similar_field(): void
    {
        Livewire::test($this->makeComponent())
            ->set('race', true)
            ->set('name', 'Books')
            ->call('saveSimilar')
            ->assertHasErrors(['name' => 'The name has already been taken.'])
            ->set('name', 'Food')
            ->call('saveSimilar')
            ->call('confirmSimilar')
            ->assertHasErrors('name')
            ->assertSet('stored', []);
    }

    public function test_unique_violation_is_rethrown_without_a_similar_field(): void
    {
        $this->expectException(UniqueConstraintViolationException::class);

        Livewire::test(new class extends Component
        {
            use ConfirmsSimilar;

            protected function findSimilar(): iterable
            {
                return [];
            }

            protected function storeSimilar()
            {
                throw new UniqueConstraintViolationException('sqlite', 'insert', [], new \Exception);
            }

            public function render()
            {
                return '<div></div>';
            }
        })->call('saveSimilar');
    }

    public function test_validates_before_searching_and_confirming(): void
    {
        Livewire::test($this->makeComponent())
            ->call('saveSimilar')
            ->assertHasErrors('name')
            ->call('confirmSimilar')
            ->assertHasErrors('name')
            ->assertSet('stored', []);
    }

    public function test_component_renders_nothing_without_items(): void
    {
        $this->assertSame('', trim(Blade::render('<tk:similar :items="[]" />')));
    }

    public function test_component_renders_items_and_actions(): void
    {
        $html = Blade::render('<tk:similar id="s" :items="[[\'id\' => 7, \'name\' => \'Foods\']]" value="Food" select="useExisting" />');

        $this->assertStringContainsString('Similar records found', $html);
        $this->assertStringContainsString('wire:key="s-7"', $html);
        $this->assertStringContainsString('<span>Foods</span>', $html);
        $this->assertStringContainsString('wire:click="useExisting(7)"', $html);
        $this->assertStringContainsString('wire:click="confirmSimilar"', $html);
        $this->assertStringContainsString('wire:click="cancelSimilar"', $html);
    }
}

class SimilarCategory extends Model
{
    protected $guarded = [];

    public $timestamps = false;
}

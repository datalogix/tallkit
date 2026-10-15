<?php

namespace TALLKit\Tests\Unit\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\ComponentAttributeBag;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

enum InteractsWithOptionsStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return ucfirst($this->value).'!';
    }
}

enum InteractsWithOptionsSize
{
    case Small;
    case Large;
}

class InteractsWithOptionsColor extends Model
{
    protected $table = 'options_colors';

    protected $guarded = [];

    public $timestamps = false;

    public function getDisplayAttribute(): string
    {
        return 'Color '.$this->name;
    }
}

class InteractsWithOptionsTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('database.default', 'testing');
    }

    protected function parse(array $attributes): array
    {
        return TALLKit::parseOptions(new ComponentAttributeBag($attributes));
    }

    protected function createColors(): void
    {
        Schema::create('options_colors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
        });

        InteractsWithOptionsColor::create(['name' => 'Red']);
        InteractsWithOptionsColor::create(['name' => 'Blue']);
        InteractsWithOptionsColor::create(['name' => null]);
    }

    public function test_options_attributes_are_removed_from_the_bag(): void
    {
        $attributes = new ComponentAttributeBag(['options' => ['a'], 'option-label' => 'x', 'id' => 'y']);

        TALLKit::parseOptions($attributes);

        $this->assertSame(['id' => 'y'], $attributes->getAttributes());
    }

    public function test_enum_class_lists_its_cases(): void
    {
        $this->assertSame(
            ['active' => 'Active!', 'inactive' => 'Inactive!'],
            $this->parse(['options' => InteractsWithOptionsStatus::class]),
        );
        $this->assertSame(
            ['Small' => 'Small', 'Large' => 'Large'],
            $this->parse(['options' => InteractsWithOptionsSize::class]),
        );
    }

    public function test_enum_cases_inside_a_list(): void
    {
        $this->assertSame(
            ['inactive' => 'Inactive!', 'Large' => 'Large'],
            $this->parse(['options' => [InteractsWithOptionsStatus::Inactive, InteractsWithOptionsSize::Large]]),
        );
    }

    public function test_strings(): void
    {
        $this->assertSame([], $this->parse(['options' => '']));
        $this->assertSame(['Only' => 'Only'], $this->parse(['options' => 'Only']));
        $this->assertSame(['stdClass' => 'stdClass'], $this->parse(['options' => \stdClass::class]));
        $this->assertSame([], $this->parse([]));
    }

    public function test_scalar_lists_use_the_value_as_key(): void
    {
        $this->assertSame(['a' => 'a', 'b' => 'b', 1 => 1], $this->parse(['options' => ['a', 'b', 1]]));
    }

    public function test_associative_arrays_keep_their_keys(): void
    {
        $this->assertSame(['br' => 'Brazil', 'us' => 'USA'], $this->parse(['options' => ['br' => 'Brazil', 'us' => 'USA']]));
        $this->assertSame([2 => 'Two', 5 => 'Five'], $this->parse(['options' => collect([2 => 'Two', 5 => 'Five'])]));
    }

    public function test_arrays_use_default_value_and_label_fields(): void
    {
        $this->assertSame(
            [1 => 'One', 'v' => 'Label', 't' => 'Title', 'x' => 'Text'],
            $this->parse(['options' => [
                ['id' => 1, 'name' => 'One'],
                ['value' => 'v', 'label' => 'Label'],
                ['value' => 't', 'title' => 'Title'],
                ['value' => 'x', 'text' => 'Text'],
            ]]),
        );
    }

    public function test_arrays_with_custom_value_and_label_fields(): void
    {
        $this->assertSame(
            ['br' => 'Brazil', 'us' => 'USA'],
            $this->parse([
                'options' => [['code' => 'br', 'country' => ['name' => 'Brazil']], ['code' => 'us', 'country' => ['name' => 'USA']]],
                'option-value' => 'code',
                'option-label' => 'country.name',
            ]),
        );
    }

    public function test_arrays_without_value_fall_back_to_key_and_without_label_to_the_item(): void
    {
        $object = (object) ['other' => 1];

        $this->assertSame(['k' => 'Name'], $this->parse(['options' => ['k' => ['name' => 'Name']]]));
        $this->assertSame([0 => $object], $this->parse(['options' => [$object]]));
    }

    public function test_models_query_and_class_name(): void
    {
        $this->createColors();

        $this->assertSame([1 => 'Red', 2 => 'Blue', 3 => 3], $this->parse(['options' => InteractsWithOptionsColor::class]));
        $this->assertSame([2 => 'Blue'], $this->parse(['options' => InteractsWithOptionsColor::where('name', 'Blue')]));
        $this->assertSame(
            [1 => 'Color Red', 2 => 'Color Blue'],
            $this->parse(['options' => InteractsWithOptionsColor::whereNotNull('name')->get(), 'option-label' => 'display']),
        );
    }

    public function test_a_single_model_instance_is_one_option(): void
    {
        $this->createColors();

        $this->assertSame([1 => 'Red'], $this->parse(['options' => InteractsWithOptionsColor::find(1)]));
    }

    public function test_groups(): void
    {
        $this->assertSame(
            [
                'Fruits' => ['apple' => 'Apple', 'pear' => 'Pear'],
            ],
            $this->parse([
                'options' => [
                    ['name' => 'Fruits', 'items' => [['id' => 'apple', 'name' => 'Apple'], ['id' => 'pear', 'name' => 'Pear']]],
                    ['name' => 'Empty', 'items' => []],
                    ['name' => 'Fruits', 'items' => [['id' => 'apple', 'name' => 'Apple'], ['id' => 'pear', 'name' => 'Pear']]],
                ],
                'option-group-children' => 'items',
            ]),
        );
    }

    public function test_groups_with_custom_fields_and_models(): void
    {
        $this->createColors();

        $this->assertSame(
            ['Warm' => [1 => 'Red'], 1 => [2 => 'Blue', 3 => 3]],
            $this->parse([
                'options' => [
                    ['title' => 'Warm', 'colors' => InteractsWithOptionsColor::whereKey(1)->get()],
                    ['colors' => InteractsWithOptionsColor::whereKey([2, 3])->get()],
                ],
                'option-group-label' => 'title',
                'option-group-children' => 'colors',
            ]),
        );
    }

    public function test_group_children_fields_and_key_fallback(): void
    {
        $this->assertSame(
            ['Letters' => ['a' => 'Alpha', 1 => 'Beta']],
            $this->parse([
                'options' => [['label' => 'Letters', 'children' => [['code' => 'a', 'text' => 'Alpha'], ['text' => 'Beta']]]],
                'option-label' => 'label',
                'option-group-children' => 'children',
                'option-group-children-value' => 'code',
                'option-group-children-label' => 'text',
            ]),
        );
    }

    public function test_nested_associative_arrays_become_groups(): void
    {
        $this->assertSame(
            ['Fruits' => ['a' => 'Apple', 'p' => 'Pear'], 'Other' => 'Other'],
            $this->parse(['options' => ['Fruits' => ['a' => 'Apple', 'p' => 'Pear'], 'Other' => 'Other']]),
        );
    }
}

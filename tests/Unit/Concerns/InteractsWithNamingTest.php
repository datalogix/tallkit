<?php

namespace TALLKit\Tests\Unit\Concerns;

use Livewire\Component;
use Livewire\Livewire;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithNamingTest extends TestCase
{
    public static array $namingCaptured = [];

    public function test_data_key_and_event_name_use_the_prefix(): void
    {
        $this->assertSame('data-tallkit-field', TALLKit::dataKey('field'));
        $this->assertSame('tallkit:open', TALLKit::eventName('open'));
    }

    public function test_data_selector_with_and_without_value(): void
    {
        $this->assertSame('[data-tallkit-tab]', TALLKit::dataSelector('tab'));
        $this->assertSame('[data-tallkit-tab]', TALLKit::dataSelector('tab', ''));
        $this->assertSame('[data-tallkit-tab="0"]', TALLKit::dataSelector('tab', 0));
        $this->assertSame('[data-tallkit-tab="a\"b\\\\c"]', TALLKit::dataSelector('tab', 'a"b\\c'));
    }

    public function test_storage_key_joins_parts(): void
    {
        $this->assertSame('tallkit', TALLKit::storageKey());
        $this->assertSame('tallkit.table.columns', TALLKit::storageKey('table', 'columns'));
    }

    public function test_generate_id_slugs_parts_and_skips_empty_ones(): void
    {
        $this->assertSame('tallkit-my-prefix-name-my-suffix', TALLKit::generateId('My Prefix', 'name', 'My Suffix'));
        $this->assertSame('tallkit-name', TALLKit::generateId(name: 'name'));
        $this->assertMatchesRegularExpression('/^tallkit-modal-[a-z0-9]{8}$/', TALLKit::generateId('modal'));
        $this->assertNotSame(TALLKit::generateId(), TALLKit::generateId());
    }

    public function test_id_part_keeps_slugs_and_hashes_anything_else(): void
    {
        $this->assertSame('email', TALLKit::idPart('email'));
        $this->assertSame('42', TALLKit::idPart(42));
        $this->assertSame('user-name-'.substr(md5('User Name'), 0, 8), TALLKit::idPart('User Name'));
        $this->assertSame(substr(md5(''), 0, 8), TALLKit::idPart(''));
        $this->assertSame(substr(md5('!!'), 0, 8), TALLKit::idPart('!!'));
    }

    public function test_short_hash(): void
    {
        $this->assertSame(substr(md5('abc'), 0, 8), TALLKit::shortHash('abc'));
        $this->assertSame(substr(md5('abc'), 0, 4), TALLKit::shortHash('abc', 4));
    }

    public function test_stable_id_counts_per_name_and_normalizes_dots_and_brackets(): void
    {
        $this->assertSame('tallkit-field-user-email', TALLKit::stableId('field', 'user.email'));
        $this->assertSame('tallkit-field-user-email-2', TALLKit::stableId('field', 'user[email]'));
        $this->assertSame('tallkit-field-user-email-label', TALLKit::stableId('field', 'user.email', 'label'));
    }

    public function test_stable_id_without_name_uses_a_counter(): void
    {
        $this->assertSame('tallkit-field-1', TALLKit::stableId('field'));
        $this->assertSame('tallkit-field-2', TALLKit::stableId('field', '...'));
        $this->assertSame('tallkit-modal-1', TALLKit::stableId('modal'));
    }

    public function test_stable_id_skips_ids_already_taken(): void
    {
        $this->assertSame('tallkit-field-1', TALLKit::stableId('field'));
        $this->assertSame('tallkit-field-1-2', TALLKit::stableId('field', '1'));
        $this->assertTrue(isset(app('tallkit.ids')['#tallkit-field-1-2']));
    }

    public function test_stable_id_is_scoped_by_the_livewire_component(): void
    {
        static::$namingCaptured = [];

        $component = Livewire::test(new class extends Component
        {
            public function render()
            {
                return <<<'BLADE'
                <div>@php(\TALLKit\Tests\Unit\Concerns\InteractsWithNamingTest::$namingCaptured[] = TALLKit::stableId('field', 'email'))</div>
                BLADE;
            }
        });

        $id = strtolower($component->id());

        $this->assertSame(["tallkit-field-{$id}-email"], static::$namingCaptured);
    }
}

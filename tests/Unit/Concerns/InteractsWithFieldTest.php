<?php

namespace TALLKit\Tests\Unit\Concerns;

use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Livewire;
use TALLKit\Facades\TALLKit;
use TALLKit\Livewire\DateRange;
use TALLKit\Tests\TestCase;

class InteractsWithFieldTest extends TestCase
{
    public static array $fieldCaptured = [];

    protected function context(array $attributes = [], null|bool|string $label = null, ?string $id = null, array $scope = []): array
    {
        return TALLKit::fieldContext(new ComponentAttributeBag($attributes), $label, $id, $scope);
    }

    protected function withOldInput(array $input): void
    {
        $session = $this->app['session.store'];
        $session->put('_old_input', $input);
        request()->setLaravelSession($session);
    }

    public function test_field_props_and_control_props(): void
    {
        $this->assertSame(
            ['value', 'id', 'size', 'label', 'labelAppend', 'labelPrepend', 'description', 'help', 'badge', 'info', 'prefix', 'suffix', 'showError', 'color', 'placeholder', 'invalid', 'bag', 'wireModel'],
            array_keys(TALLKit::fieldProps()),
        );
        $this->assertSame([], array_filter(TALLKit::fieldProps(), fn ($value) => $value !== null));
        $this->assertSame(
            ['prepend' => null, 'append' => null, 'icon' => null, 'iconTrailing' => null, 'kbd' => null, 'loading' => null],
            TALLKit::fieldControlProps(),
        );
    }

    public function test_field_context_without_attributes(): void
    {
        $this->assertSame([null, null, null, null, false, false, 'tallkit-field-1'], $this->context());
    }

    public function test_field_context_derives_field_name_label_and_id_from_name(): void
    {
        $attributes = new ComponentAttributeBag(['name' => 'user[address][street_id]', 'placeholder' => true, 'type' => 'text']);

        $this->assertSame(
            ['user[address][street_id]', 'user.address.street_id', 'Street', 'Street', false, false, 'tallkit-field-user-address-street-id'],
            TALLKit::fieldContext($attributes),
        );
        $this->assertSame(['type' => 'text'], $attributes->getAttributes());
    }

    public function test_field_context_label_skips_wildcards_and_indexes(): void
    {
        $this->assertSame('Full Name', $this->context(['name' => 'items[][full_name]'])[2]);
        $this->assertSame('items.*.full_name', $this->context(['name' => 'items[][full_name]'])[1]);
        $this->assertSame('Tags', $this->context(['name' => 'tags[]'])[2]);
        $this->assertSame('Email', $this->context(['name' => 'emails.0.email'])[2]);
        $this->assertSame('0', $this->context(['name' => '0'])[2]);
    }

    public function test_field_context_keeps_given_label_and_id(): void
    {
        $this->assertSame('Custom', $this->context(['name' => 'email'], 'Custom')[2]);
        $this->assertFalse($this->context(['name' => 'email'], false)[2]);
        $this->assertSame('Email', $this->context(['name' => 'email'], true)[2]);
        $this->assertSame('my-id', $this->context(['name' => 'email'], id: 'my-id')[6]);
        $this->assertSame('Typed', $this->context(['placeholder' => 'Typed'])[3]);
    }

    public function test_field_context_name_falls_back_to_wire_model_and_x_model(): void
    {
        $this->assertSame(['profile.name', 'profile.name'], array_slice($this->context(['wire:model.live' => 'profile.name']), 0, 2));
        $this->assertSame(['form.email', 'form.email'], array_slice($this->context(['x-model' => 'form.email']), 0, 2));
        $this->assertSame('real', $this->context(['name' => 'real', 'wire:model' => 'other'])[0]);
    }

    public function test_field_context_invalid_and_bag(): void
    {
        app('view')->share('errors', (new ViewErrorBag)
            ->put('default', new MessageBag(['email' => ['Bad']]))
            ->put('login', new MessageBag(['password' => ['Bad']])));

        $this->assertTrue($this->context(['name' => 'email'])[4]);
        $this->assertFalse($this->context(['name' => 'password'])[4]);
        $this->assertTrue($this->context(['name' => 'password', 'bag' => 'login'])[4]);
        $this->assertTrue($this->context(['name' => 'password', 'error:bag' => 'login'])[4]);
        $this->assertTrue($this->context(['name' => 'password'], scope: ['bag' => 'login'])[4]);
        $this->assertSame('forced', $this->context(['name' => 'password', 'invalid' => 'forced'])[4]);
        $this->assertFalse($this->context(['name' => 'email'], scope: ['invalid' => false])[4]);
    }

    public function test_field_context_stores_the_bag_by_id(): void
    {
        $id = $this->context(['name' => 'password', 'bag' => 'login'])[6];

        $this->assertSame('login', TALLKit::fieldErrorBag($id));
        $this->assertNull(TALLKit::fieldErrorBag($this->context(['name' => 'email'])[6]));
        $this->assertNull(TALLKit::fieldErrorBag(null));
    }

    public function test_field_context_scope_placeholder(): void
    {
        $this->assertSame('Email', $this->context(['name' => 'email', 'placeholder' => 'x'], scope: ['placeholder' => true])[3]);
    }

    public function test_field_context_auto_wire_model_inside_livewire(): void
    {
        static::$fieldCaptured = [];

        Livewire::test(new class extends Component
        {
            public array $user = ['name' => ''];

            public string $email = '';

            #[Locked]
            public string $role = '';

            protected string $secret = '';

            public function render()
            {
                return <<<'BLADE'
                <div>@php
                    $ctx = fn (array $attributes, array $scope = []) => TALLKit::fieldContext(new \Illuminate\View\ComponentAttributeBag($attributes), scope: $scope)[5];
                    \TALLKit\Tests\Unit\Concerns\InteractsWithFieldTest::$fieldCaptured = [
                        'email' => $ctx(['name' => 'email']),
                        'nested' => $ctx(['name' => 'user[name]']),
                        'wildcard' => $ctx(['name' => 'user[][name]']),
                        'locked' => $ctx(['name' => 'role']),
                        'protected' => $ctx(['name' => 'secret']),
                        'missing' => $ctx(['name' => 'missing']),
                        'disabled' => $ctx(['name' => 'email', 'wire-model' => false]),
                        'scope-disabled' => $ctx(['name' => 'email'], ['wireModel' => false]),
                        'has-wire-model' => $ctx(['name' => 'email', 'wire:model' => 'email']),
                        'has-x-model' => $ctx(['name' => 'email', 'x-model' => 'email']),
                        'no-name' => $ctx([]),
                    ];
                @endphp</div>
                BLADE;
            }
        });

        $this->assertSame([
            'email' => 'email',
            'nested' => 'user.name',
            'wildcard' => false,
            'locked' => false,
            'protected' => false,
            'missing' => false,
            'disabled' => false,
            'scope-disabled' => false,
            'has-wire-model' => false,
            'has-x-model' => false,
            'no-name' => false,
        ], static::$fieldCaptured);
    }

    public function test_field_old_value(): void
    {
        $this->assertSame('default', TALLKit::fieldOldValue('name', 'default'));

        $this->withOldInput(['name' => 'Old', 'password' => 'secret', 'user' => ['email' => 'a@b.c']]);

        $this->assertSame('Old', TALLKit::fieldOldValue('name', 'default'));
        $this->assertSame('a@b.c', TALLKit::fieldOldValue('user.email'));
        $this->assertSame('default', TALLKit::fieldOldValue('missing', 'default'));
        $this->assertSame('default', TALLKit::fieldOldValue('password', 'default', 'password'));
        $this->assertSame('default', TALLKit::fieldOldValue('name', 'default', 'file'));
        $this->assertSame('default', TALLKit::fieldOldValue(null, 'default'));
        $this->assertSame('default', TALLKit::fieldOldValue('', 'default'));
    }

    public function test_field_old_value_is_ignored_inside_livewire(): void
    {
        $this->withOldInput(['name' => 'Old']);
        static::$fieldCaptured = [];

        Livewire::test(new class extends Component
        {
            public function render()
            {
                return <<<'BLADE'
                <div>@php(\TALLKit\Tests\Unit\Concerns\InteractsWithFieldTest::$fieldCaptured = [TALLKit::fieldOldValue('name', 'live'), TALLKit::fieldOldChecked('name', true)])</div>
                BLADE;
            }
        });

        $this->assertSame(['live', true], static::$fieldCaptured);
    }

    public function test_field_date_value(): void
    {
        $date = Carbon::parse('2026-10-15 14:30:00');

        $this->assertSame('2026-10-15', TALLKit::fieldDateValue($date));
        $this->assertSame('15/10/2026 14:30', TALLKit::fieldDateValue($date, 'd/m/Y H:i'));
        $this->assertSame(
            ['start' => '2026-01-01', 'end' => '2026-01-31'],
            TALLKit::fieldDateValue(DateRange::between('2026-01-01', '2026-01-31')),
        );
        $this->assertSame(['start' => '2026-01-01', 'end' => null], TALLKit::fieldDateValue(DateRange::between('2026-01-01')));
        $this->assertSame(['a' => '2026-10-15', 'b' => 'x', 'c' => ['2026-10-15']], TALLKit::fieldDateValue(['a' => $date, 'b' => 'x', 'c' => collect([$date])]));
        $this->assertSame('2026-10-15', TALLKit::fieldDateValue('2026-10-15'));
        $this->assertNull(TALLKit::fieldDateValue(null));
    }

    public function test_field_old_checked(): void
    {
        $this->assertTrue(TALLKit::fieldOldChecked('agree', true));
        $this->assertFalse(TALLKit::fieldOldChecked('agree', false));

        $this->withOldInput(['agree' => 'on', 'color' => 'red', 'tags' => [1, '2'], 'count' => 3]);

        $this->assertTrue(TALLKit::fieldOldChecked(null, true));
        $this->assertTrue(TALLKit::fieldOldChecked('agree', false));
        $this->assertFalse(TALLKit::fieldOldChecked('agree', true, 'yes'));
        $this->assertTrue(TALLKit::fieldOldChecked('color', false, 'red'));
        $this->assertFalse(TALLKit::fieldOldChecked('color', true, 'blue'));
        $this->assertTrue(TALLKit::fieldOldChecked('tags', false, 1));
        $this->assertTrue(TALLKit::fieldOldChecked('tags', false, 2));
        $this->assertFalse(TALLKit::fieldOldChecked('tags', true, 3));
        $this->assertTrue(TALLKit::fieldOldChecked('count', false, 3));
        $this->assertFalse(TALLKit::fieldOldChecked('missing', true));
    }

    public function test_field_described_by(): void
    {
        $this->assertNull(TALLKit::fieldDescribedBy(null, 'd', 'h', true));
        $this->assertNull(TALLKit::fieldDescribedBy('f'));
        $this->assertSame('f-description f-help f-hint f-error', TALLKit::fieldDescribedBy('f', 'd', 'h', true, null, 'x'));
        $this->assertSame('f-help', TALLKit::fieldDescribedBy('f', help: 'h', invalid: true, showError: false));
        $this->assertSame('f-error', TALLKit::fieldDescribedBy('f', invalid: true, showError: true));
    }

    public function test_field_invalid(): void
    {
        $this->assertSame(['aria-invalid' => 'true', 'data-invalid' => true], TALLKit::fieldInvalid(true));
        $this->assertSame(['aria-invalid' => null, 'data-invalid' => null], TALLKit::fieldInvalid(false));
        $this->assertSame(['aria-invalid' => null, 'data-invalid' => null], TALLKit::fieldInvalid(null));
    }

    public function test_field_type(): void
    {
        $expected = [
            '' => 'text',
            'name' => 'text',
            'chat' => 'text',
            'favorite_color' => 'color',
            'created_at' => 'datetime-local',
            'startDateTime' => 'datetime-local',
            'birthDate' => 'date',
            'published_on' => 'date',
            'userEmail' => 'email',
            'website' => 'url',
            'avatar_image' => 'file',
            'new_password_confirmation' => 'password',
            'start_time' => 'time',
            'WhatsApp' => 'text',
            'whatsapp' => 'tel',
            'user[mobile]' => 'tel',
        ];

        foreach ($expected as $name => $type) {
            $this->assertSame($type, TALLKit::fieldType((string) $name), $name);
        }

        $this->assertSame('text', TALLKit::fieldType());
    }

    public function test_field_type_keeps_working_after_many_distinct_names(): void
    {
        for ($i = 0; $i < 1100; $i++) {
            TALLKit::fieldType("field_{$i}");
        }

        $this->assertSame('email', TALLKit::fieldType('contact_email'));
    }

    public function test_field_mask_false_or_unsupported_type(): void
    {
        app()->setLocale('pt_BR');

        $this->assertNull(TALLKit::fieldMask('cpf', false, 'text'));
        $this->assertNull(TALLKit::fieldMask('cpf'));
        $this->assertNull(TALLKit::fieldMask('cpf', null, 'email'));
        $this->assertNull(TALLKit::fieldMask(null, null, 'text'));
    }

    public function test_field_mask_by_name_and_type_for_the_locale(): void
    {
        app()->setLocale('pt_BR');

        $this->assertSame('999.999.999-99', TALLKit::fieldMask('user_cpf', null, 'text'));
        $this->assertSame('99999-999', TALLKit::fieldMask('zipCode', null, 'text'));
        $this->assertSame('(99) 999999999', TALLKit::fieldMask('contact', null, 'tel'));
        $this->assertNull(TALLKit::fieldMask('nickname', null, 'text'));
    }

    public function test_field_mask_for_a_locale_without_masks(): void
    {
        app()->setLocale('en');

        $this->assertNull(TALLKit::fieldMask('user_cpf', null, 'text'));
        $this->assertSame('999.999.999-99', TALLKit::fieldMask(mask: 'cpf'));
    }

    public function test_field_mask_named_or_literal(): void
    {
        app()->setLocale('pt_BR');

        $this->assertSame('999.999.999-99', TALLKit::fieldMask('anything', 'cpf', 'email'));
        $this->assertSame('**.***.***/****-99', TALLKit::fieldMask(mask: 'cnpj'));
        $this->assertSame('999-aaa', TALLKit::fieldMask(mask: '999-aaa'));
    }

    public function test_field_mask_falls_back_to_the_language(): void
    {
        config(['tallkit.masks' => ['pt' => ['99.99' => ['code']], 'en_US' => ['99-99' => ['code']]]]);

        app()->setLocale('pt_BR');
        $this->assertSame('99.99', TALLKit::fieldMask('code', null, 'text'));

        app()->setLocale('en_US');
        $this->assertSame('99-99', TALLKit::fieldMask('code', null, 'text'));
    }

    public function test_field_mask_with_masks_not_keyed_by_locale(): void
    {
        config(['tallkit.masks' => ['999' => ['code'], 'AA' => ['initials']]]);

        $this->assertSame('999', TALLKit::fieldMask('zip_code', null, 'text'));
        $this->assertSame('AA', TALLKit::fieldMask(mask: 'initials'));
    }

    public function test_field_mask_without_masks(): void
    {
        config(['tallkit.masks' => null]);

        $this->assertNull(TALLKit::fieldMask('cpf', null, 'text'));
        $this->assertSame('cpf', TALLKit::fieldMask(mask: 'cpf'));
    }
}

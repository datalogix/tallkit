@props([
    'currency' => null,
    'symbol' => null,
    'delimiter' => null,
    'thousands' => null,
    'position' => null,
    'placeholder' => null,
    'prefix' => null,
    'suffix' => null,
    'precision' => null,
    'as' => null,
])
@php

$currencies = [
    'BRL' => [
        'symbol' => 'R$',
        'delimiter' => ',',
        'thousands' => '.',
        'position' => 'prefix',
        'placeholder' => '0,00',
        'precision' => 2,
    ],

    'USD' => [
        'symbol' => '$',
        'delimiter' => '.',
        'thousands' => ',',
        'position' => 'prefix',
        'placeholder' => '0.00',
        'precision' => 2,
    ],

    'GBP' => [
        'symbol' => '£',
        'delimiter' => '.',
        'thousands' => ',',
        'position' => 'prefix',
        'placeholder' => '0.00',
        'precision' => 2,
    ],

    'JPY' => [
        'symbol' => '¥',
        'delimiter' => '',
        'thousands' => ',',
        'position' => 'prefix',
        'placeholder' => '0',
        'precision' => 0,
    ],

    'EUR' => [
        'symbol' => '€',
        'delimiter' => ',',
        'thousands' => '.',
        'position' => 'prefix',
        'placeholder' => '0,00',
        'precision' => 2,
    ],
];

$currency ??= match (TALLKit::locale()) {
    'pt_BR' => 'BRL',
    'pt_PT' => 'EUR',
    'en_US', 'en' => 'USD',
    'en_GB' => 'GBP',
    'ja_JP' => 'JPY',
    default => match (Str::before(TALLKit::locale(), '_')) {
        'pt' => 'BRL',
        'en' => 'USD',
        'ja' => 'JPY',
        default => 'EUR',
    },
};

if ($config = data_get($currencies, Str::upper($currency))) {
    $symbol ??= $config['symbol'];
    $delimiter ??= $config['delimiter'];
    $thousands ??= $config['thousands'];
    $position ??= $config['position'];
    $placeholder ??= $config['placeholder'];
    $precision ??= $config['precision'];
}

$precision = (int) ($precision ?? ($delimiter === '' ? 0 : 2));
// The mask needs a decimal separator even with no decimals: one that isn't the thousands'.
$delimiter = (string) $delimiter !== '' ? $delimiter : ($thousands === '.' ? ',' : '.');
$as = $as === 'cents' ? 'cents' : 'decimal';

// No wire:model on the field (it would send the formatted text): the script sets the property with the amount.
$wireModel = $attributes->whereStartsWith('wire:model');
$wireModelKey = array_key_first($wireModel->getAttributes());
[$name, $fieldName, , , , $autoWireModel] = TALLKit::fieldContext(attributes: new \Illuminate\View\ComponentAttributeBag($attributes->getAttributes()), label: false, id: 'money');
$model = $wireModel->first() ?: ($autoWireModel ?: null);
$modifiers = $wireModelKey ? Str::after($wireModelKey, 'wire:model') : '';

$value = in_livewire() ? null : TALLKit::fieldOldValue($fieldName, $attributes->get('value'));

if (is_numeric($value)) {
    $amount = $as === 'cents' ? $value / (10 ** $precision) : $value;
    $value = number_format((float) $amount, $precision, $delimiter, $thousands ?? '');
}

@endphp
<tk:input
    :attributes="TALLKit::fieldWithProps(attributes: $attributes->whereDoesntStartWith(['wire:model', 'value']), scope: get_defined_vars())"
    :$name
    :$value
    :$placeholder
    :prefix="$prefix ?? ($position === 'prefix' ? $symbol : null)"
    :suffix="$suffix ?? ($position === 'suffix' ? $symbol : null)"
    :old="false"
    :mask="false"
    :wire-model="false"
    :inputmode="$precision > 0 ? 'decimal' : 'numeric'"
    x-data="money({{ Js::from(compact('delimiter', 'thousands', 'precision', 'as', 'model', 'modifiers')) }})"
    x-mask:dynamic="$money($input, {{ Js::from($delimiter) }}, {{ Js::from($thousands) }}, {{ $precision }})"
/>

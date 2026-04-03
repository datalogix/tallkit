@props([
    'currency' => null,
    'symbol' => null,
    'delimiter' => null,
    'thousands' => null,
    'position' => null,
    'placeholder' => null,
])
@php

$currencies = [
    'BRL' => [
        'symbol' => 'R$',
        'delimiter' => ',',
        'thousands' => '.',
        'position' => 'prefix',
        'placeholder' => '0,00',
    ],

    'USD' => [
        'symbol' => '$',
        'delimiter' => '.',
        'thousands' => ',',
        'position' => 'prefix',
        'placeholder' => '0.00',
    ],

    'GBP' => [
        'symbol' => '£',
        'delimiter' => '.',
        'thousands' => ',',
        'position' => 'prefix',
        'placeholder' => '0.00',
    ],

    'JPY' => [
        'symbol' => '¥',
        'delimiter' => '',
        'thousands' => ',',
        'position' => 'prefix',
        'placeholder' => '0',
    ],

    'EUR' => [
        'symbol' => '€',
        'delimiter' => '.',
        'thousands' => ',',
        'position' => 'prefix',
        'placeholder' => '0.00',
    ],
];

$currency ??= match (app()->getLocale()) {
    'pt_BR' => 'BRL',
    'en_US', 'en' => 'USD',
    'en_GB' => 'GBP',
    'ja_JP' => 'JPY',
    default => 'EUR',
};

if ($config = data_get($currencies, Str::upper($currency))) {
    $symbol ??= $config['symbol'];
    $delimiter ??= $config['delimiter'];
    $thousands ??= $config['thousands'];
    $position ??= $config['position'];
    $placeholder ??= $config['placeholder'];
}

@endphp
<tk:input
    :$attributes
    :$placeholder
    :prefix="$position === 'prefix' ? $symbol : null"
    :suffix="$position === 'suffix' ? $symbol : null"
    x-mask:dynamic="$money($input, '{{ $delimiter }}', '{{ $thousands }}')"
/>

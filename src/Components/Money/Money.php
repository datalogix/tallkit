<?php

namespace TALLKit\Components\Money;

use Illuminate\Support\Str;
use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Money extends BladeComponent
{
    public function __construct(
        public ?string $currency = null,
        public ?string $symbol = null,
        public ?string $delimiter = null,
        public ?string $thousands = null,
        public ?string $position = null,
        public ?string $placeholder = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $currencies = $this->getCurrencies();

        $this->currency ??= match (app()->getLocale()) {
            'pt_BR' => 'BRL',
            'en_US', 'en' => 'USD',
            'en_GB' => 'GBP',
            'ja_JP' => 'JPY',
            default => 'EUR',
        };

        if ($config = data_get($currencies, Str::upper($this->currency))) {
            $this->symbol ??= $config['symbol'];
            $this->delimiter ??= $config['delimiter'];
            $this->thousands ??= $config['thousands'];
            $this->position ??= $config['position'];
            $this->placeholder ??= $config['placeholder'];
        }
    }

    protected function getCurrencies()
    {
        return [
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
    }
}

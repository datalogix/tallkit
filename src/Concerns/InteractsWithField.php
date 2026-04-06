<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use TALLKit\Facades\TALLKit;

trait InteractsWithField
{
    public function fieldAttributes(ComponentAttributeBag $attributes)
    {
        $wireModel = $attributes->whereStartsWith('wire:model')->first();
        $xModel = $attributes->whereStartsWith('x-model')->first();

        $name = $attributes->pluck('name', $wireModel ?? $xModel);
        $fieldName = Str::replace(['[', ']'], ['.', ''], Str::before($name, '[]'));

        $label = $attributes->pluck('label');
        $label = $label === true || $label === null ? Str::headline(Str::before($fieldName, '_id')) : $label;

        $placeholder = $attributes->pluck('placeholder');
        $placeholder = $placeholder === true ? $label : $placeholder;

        $invalid = $attributes->pluck('invalid', $name && TALLKit::hasError($name));
        $wireModel = ! $wireModel && in_livewire() && $fieldName && ! $xModel ? $fieldName : false;

        return [
            $name,
            $fieldName,
            $label,
            $placeholder,
            $invalid,
            $wireModel,
        ];
    }

    public function detectInputType(?string $name = null)
    {
        if (blank($name)) {
            return 'text';
        }

        $types = [
            'color' => ['color'],
            'date' => ['date', 'birthdate', 'birth_date', '_at'],
            'datetime-local' => ['datetime', 'date_time'],
            'email' => ['email'],
            'file' => ['image', 'picture', 'photo', 'logo', 'background', 'audio', 'video', 'file', 'document'],
            'password' => ['password', 'password_confirmation', 'new_password', 'new_password_confirmation'],
            'url' => ['url', 'website', 'youtube', 'vimeo', 'facebook', 'twitter', 'instagram', 'linkedin'],
            'time' => ['time', 'hour'],
            'tel' => ['phone', 'whatsapp'],
        ];

        foreach ($types as $type => $names) {
            if (Str::contains($name, $names, true)) {
                return $type;
            }
        }

        return 'text';
    }

    public function detectInputMask(
        ?string $name = null,
        null|string|bool $mask = null,
        ?string $type = null
    ) {
        if ($mask === false || ! in_array($type, ['text', 'tel'])) {
            return null;
        }

        $masks = [
            '99999-999' => ['cep', 'zipcode', 'zip-code'],
            '99/99/9999' => ['date', 'birthdate', 'birth_date', '_at'],
            '99/99/9999 99:99' => ['datetime', 'date_time'],
            '99:99' => ['time'],
            '999.999.999-99' => ['cpf'],
            '99.999.999/9999-99' => ['cnpj'],
            '(99) 999999999' => ['tel', 'phone', 'whatsapp'],
        ];

        if (is_string($mask)) {
            foreach ($masks as $maskValue => $names) {
                if (Str::contains($mask, $names, true)) {
                    return $maskValue;
                }
            }

            return $mask;
        }

        if (blank($name) && blank($type)) {
            return null;
        }

        foreach ($masks as $maskValue => $names) {
            if (Str::contains($name, $names, true) || Str::contains($type, $names, true)) {
                return $maskValue;
            }
        }

        return null;
    }
}

<?php

namespace TALLKit\Concerns;

use DateTimeInterface;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use TALLKit\Livewire\DateRange;

trait InteractsWithField
{
    public function fieldProps(): array
    {
        return [
            'value' => null,
            'id' => null,
            'size' => null,
            'label' => null,
            'labelAppend' => null,
            'labelPrepend' => null,
            'description' => null,
            'help' => null,
            'badge' => null,
            'info' => null,
            'prefix' => null,
            'suffix' => null,
            'showError' => null,
            'color' => null,
            'placeholder' => null,
            'invalid' => null,
            'bag' => null,
            'wireModel' => null,
        ];
    }

    public function fieldControlProps(): array
    {
        return [
            'prepend' => null,
            'append' => null,
            'icon' => null,
            'iconTrailing' => null,
            'kbd' => null,
            'loading' => null,
        ];
    }

    public function fieldExcludedPrefixes(array $extra = []): array
    {
        return [
            'field:', 'label:', 'info:', 'badge:', 'description:',
            'group:', 'prefix:', 'suffix:',
            'help:', 'error:',
            'control:',
            'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:', 'tooltip:',
            ...$extra,
        ];
    }

    public function fieldWithProps(
        ComponentAttributeBag $attributes,
        array $scope
    ): ComponentAttributeBag {
        return $this->attributesWithProps(
            $attributes,
            $scope,
            $this->fieldProps(),
            $this->fieldControlProps(),
        );
    }

    public function fieldContext(
        ComponentAttributeBag $attributes,
        null|bool|string $label = null,
        ?string $id = null,
        array $scope = [],
    ): array {
        $wireModel = $attributes->whereStartsWith('wire:model')->first();
        $xModel = $attributes->whereStartsWith('x-model')->first();

        $name = $attributes->pluck('name', $wireModel ?? $xModel);
        $fieldName = $name ? $this->errorKey($name) : $name;

        $labelName = $fieldName ? collect(explode('.', $fieldName))->reject(fn ($part) => $part === '*' || is_numeric($part))->last() : null;
        $label = $label === true || $label === null ? ($labelName ? Str::headline(preg_replace('/_id$/', '', $labelName)) : $fieldName) : $label;

        $placeholder = $scope['placeholder'] ?? $attributes->pluck('placeholder');
        $placeholder = $placeholder === true ? $label : $placeholder;

        $bag = $this->errorBagName($scope['bag'] ?? $attributes->pluck('bag') ?? $attributes->get('error:bag'));
        $invalid = $scope['invalid'] ?? $attributes->pluck('invalid', fn () => $name && $this->hasError($name, $bag));
        // Bound only when the component has the property: binding a missing one breaks on the first change.
        $autoWireModel = ($scope['wireModel'] ?? $attributes->pluck('wire-model')) !== false;
        $wireModel = $autoWireModel && ! $wireModel && ! $xModel && $fieldName && ! Str::contains($fieldName, '*') && $this->livewireRendering() && $this->livewireHasProperty($fieldName)
            ? $fieldName
            : false;
        $id ??= $this->fieldId($fieldName ?: null);

        if ($bag !== null) {
            app('tallkit.field-bags')[$id] = $bag;
        }

        return [
            $name,
            $fieldName,
            $label,
            $placeholder,
            $invalid,
            $wireModel,
            $id,
        ];
    }

    public function fieldErrorBag(?string $id): ?string
    {
        return $id !== null ? (app('tallkit.field-bags')[$id] ?? null) : null;
    }

    protected function fieldId(?string $fieldName = null): string
    {
        return $this->stableId('field', $fieldName);
    }

    /** Passwords and files are never sent back. */
    public function fieldOldValue(?string $fieldName, mixed $value = null, ?string $type = null): mixed
    {
        if (blank($fieldName) || in_array($type, ['password', 'file'], true) || ! $this->hasOldInput()) {
            return $value;
        }

        return request()->session()->hasOldInput($fieldName) ? request()->old($fieldName) : $value;
    }

    /** Formatted in its own timezone: as JSON a Carbon becomes UTC and can shift the day. */
    public function fieldDateValue(mixed $value, string $format = 'Y-m-d'): mixed
    {
        return match (true) {
            $value instanceof DateTimeInterface => $value->format($format),
            $value instanceof DateRange => [
                'start' => $value->start()?->format($format),
                'end' => $value->end()?->format($format),
            ],
            is_iterable($value) => collect($value)->map(fn ($item) => $this->fieldDateValue($item, $format))->all(),
            default => $value,
        };
    }

    public function fieldOldChecked(?string $fieldName, bool $checked, mixed $value = null): bool
    {
        if (blank($fieldName) || ! $this->hasOldInput()) {
            return $checked;
        }

        $old = request()->old($fieldName);
        $sent = (string) ($value ?? 'on');

        return is_array($old)
            ? in_array($sent, array_map('strval', $old), true)
            : $old !== null && (string) $old === $sent;
    }

    protected function hasOldInput(): bool
    {
        return ! $this->livewireRendering()
            && request()->hasSession()
            && request()->session()->has('_old_input');
    }

    public function fieldDescribedBy(
        ?string $id,
        mixed $description = null,
        mixed $help = null,
        mixed $invalid = null,
        mixed $showError = null,
        mixed $hint = null,
    ): ?string {
        if (! $id) {
            return null;
        }

        $ids = collect([
            $description ? "{$id}-description" : null,
            $help ? "{$id}-help" : null,
            $hint ? "{$id}-hint" : null,
            $invalid && $showError !== false ? "{$id}-error" : null,
        ])->filter();

        return $ids->isNotEmpty() ? $ids->implode(' ') : null;
    }

    public function fieldType(?string $name = null): string
    {
        if (blank($name)) {
            return 'text';
        }

        $types = [
            'color' => ['color'],
            'datetime-local' => ['datetime', 'date_time', '_at'],
            'date' => ['date', 'birthdate', 'birth_date', '_on'],
            'email' => ['email'],
            'url' => ['url', 'website', 'youtube', 'vimeo', 'facebook', 'twitter', 'instagram', 'linkedin'],
            'file' => ['image', 'picture', 'photo', 'logo', 'background', 'audio', 'video', 'file'],
            'password' => ['password', 'password_confirmation', 'new_password', 'new_password_confirmation'],
            'time' => ['time', 'hour'],
            'tel' => ['phone', 'telephone', 'cellphone', 'mobile', 'whatsapp'],
        ];

        foreach ($types as $type => $names) {
            if ($this->fieldNameMatches($name, $names)) {
                return $type;
            }
        }

        return 'text';
    }

    protected function fieldNameMatches(?string $name, array $needles): bool
    {
        if (blank($name)) {
            return false;
        }

        $words = $this->fieldNameWords($name);

        foreach ($needles as $needle) {
            $matches = Str::startsWith($needle, '_')
                ? Str::endsWith($words, $this->fieldNameWords($needle))
                : Str::contains($words, $this->fieldNameWords($needle));

            if ($matches) {
                return true;
            }
        }

        return false;
    }

    protected function fieldNameWords(string $name): string
    {
        $name = preg_replace('/(?<=[a-z0-9])(?=[A-Z])/', '_', $name);
        $words = array_filter(preg_split('/[^a-z0-9]+/', Str::lower($name)), 'strlen');

        return '_'.implode('_', $words).'_';
    }

    public function fieldMask(
        ?string $name = null,
        null|string|bool $mask = null,
        ?string $type = null
    ): ?string {
        if ($mask === false) {
            return null;
        }

        $masks = (array) config('tallkit.masks', []);

        if (is_string($mask)) {
            foreach ($masks as $maskValue => $names) {
                if ($this->fieldNameMatches($mask, $names)) {
                    return (string) $maskValue;
                }
            }

            return $mask;
        }

        if (! in_array($type, ['text', 'tel']) || (blank($name) && blank($type))) {
            return null;
        }

        foreach ($masks as $maskValue => $names) {
            if ($this->fieldNameMatches($name, $names) || $this->fieldNameMatches($type, $names)) {
                return (string) $maskValue;
            }
        }

        return null;
    }
}

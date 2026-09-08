@props([
    ...TALLKit::fieldProps(),
    'align' => null,
    'checked' => null,
    'iconOn' => null,
    'iconOff' => null,
    'labelOn' => null,
    'labelOff' => null,
    'group' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label, id: $id);
$checked = is_array($checked) ? in_array($value, $checked) : (bool) $checked;
$hasStateLabel = $labelOn || $labelOff;

@endphp
<tk:field.wrapper
    :inline="!$hasStateLabel"
    :$align
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
    :label="$slot->isEmpty() ? $label : $slot"
>
    @if ($hasStateLabel)
        <div
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'state-group:')
                    ->classes([
                        'flex items-center has-[input:disabled]:cursor-not-allowed',
                        '[&_.label-checked]:hidden [&_.label-unchecked]:inline has-[input:checked]:[&_.label-checked]:inline has-[input:checked]:[&_.label-unchecked]:hidden',
                        TALLKit::gap(size: $size, mode: 'small'),
                    ])
            }}
        >
    @endif
    <label
        {{ $attributes->only('disabled')->dataKey('control') }}
        {{
            TALLKit::attributesAfter(attributes: $attributes, prefix: 'control:')
                ->classes(
                    '
                        tk-control-transition
                        tk-control-focus-ring-self
                        tk-control-invalid-ring-self

                        rounded-full
                        inline-flex
                        relative
                        overflow-hidden
                        [print-color-adjust:exact]

                        bg-zinc-300
                        dark:bg-transparent

                        ring
                        ring-zinc-300
                        dark:ring-white/20

                        has-[input:not(:disabled)]:hover:opacity-80
                        dark:has-[input:not(:disabled)]:hover:opacity-80

                        has-[input:disabled]:opacity-20
                        has-[input:disabled:checked]:opacity-10

                        dark:has-[input:disabled]:opacity-30
                        dark:has-[input:disabled:checked]:opacity-20

                        has-[input:disabled]:cursor-not-allowed
                    ',
                    TALLKit::generateClassBySize(size: $size, name: 'w', values: ['8', '10', '12', '14', '16', '18', '22']),
                    TALLKit::generateClassBySize(size: $size, name: 'h', values: ['5', '6', '7', '8', '9', '10', '12']),
                    TALLKit::iconSize(size: $size),
                    match ($color) {
                        'accent' => '
                            has-[input:checked]:bg-[var(--color-accent)]
                            has-[input:checked]:[&_span]:bg-[var(--color-accent-foreground)]
                            has-[input:checked]:[&_span]:text-[var(--color-accent-content)]
                        ',
                        default => TALLKit::checkedBackground(color: $color, wrapped: true)
                            ?? 'has-[input:checked]:bg-zinc-800 dark:has-[input:checked]:bg-white dark:has-[input:checked]:[&_span]:bg-zinc-800',
                    },
                )
        }}
    >
        <input
            @checked($checked)
            type="checkbox"
            role="switch"
            {{
                $attributes
                    ->dataKey('toggle')
                    ->merge([
                        'name' => $name,
                        'id' => $id,
                        'value' => $value,
                        'wire:model' => $wireModel,
                        TALLKit::dataKey(name: 'toggle-group') => $group,
                        'aria-label' => ($label || $hasStateLabel) ? null : __('Toggle'),
                        'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                        'aria-invalid' => $invalid ? 'true' : null,
                        'data-invalid' => $invalid ? true : null,
                    ])
                    ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: ['icon-on:', 'icon-off:', 'state:', 'state-group:']))
                    ->classes('sr-only peer')
            }}
        />
        <span
            aria-hidden="true"
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')
                    ->classes(
                        '
                            bg-white
                            absolute
                            top-1
                            start-1
                            rounded-full
                            shadow-sm
                            pointer-events-none

                            tk-control-transition

                            flex
                            items-center
                            justify-center

                            peer-checked:translate-x-full
                            rtl:peer-checked:-translate-x-full
                            peer-active:scale-95
                            peer-checked:shadow-md

                            [&_.checked]:hidden
                            [&_.unchecked]:inline
                            peer-checked:[&_.checked]:inline
                            peer-checked:[&_.unchecked]:hidden

                            [:where(&)]:text-zinc-800
                        ',
                        TALLKit::generateClassBySize(size: $size, name: 'size', values: ['3', '4', '5', '6', '7', '8', '10']),
                    )
            }}
        >
            @if ($iconOn)
                <tk:icon
                    :icon="$iconOn"
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-on:')->classes('checked')"
                />
            @endif

            @if ($iconOff)
                <tk:icon
                    :icon="$iconOff"
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-off:')->classes('unchecked')"
                />
            @endif
        </span>
    </label>
    @if ($hasStateLabel)
            <label
                {{
                    TALLKit::attributesAfter(attributes: $attributes, prefix: 'state:')
                        ->merge([
                            'for' => $id,
                        ])
                        ->classes(
                            'cursor-pointer select-none',
                            TALLKit::fontSize(size: $size)
                        )
                }}
            >
                <span class="label-checked">{{ $labelOn }}</span>
                <span class="label-unchecked">{{ $labelOff }}</span>
            </label>
        </div>
    @endif
</tk:field.wrapper>

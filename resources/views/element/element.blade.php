@props([
    'name' => null,
    'label' => null,
    'href' => null,
    'external' => null,
    'route' => null,
    'parameters' => null,
    'navigate' => null,
    'action' => null,
    'as' => null,
    'type' => null,
    'exact' => null,
    'current' => null,
    'iconDot' => null,
    'icon' => null,
    'prefix' => null,
    'suffix' => null,
    'iconTrailing' => null,
    'info' => null,
    'badge' => null,
    'kbd' => null,
    'prepend' => null,
    'append' => null,
    'ariaLabel' => null,
    'tooltip' => null,
])
@php

$as ??= 'span';
$href ??= route_detect($route, $parameters, $href);
$ariaLabel = $ariaLabel === true || $ariaLabel === null ? (is_string($label) && $label !== '' ? $label : $tooltip) : $ariaLabel;
$current ??= is_current_href($href, $exact);

if ($href) {
    $as = 'a';
} elseif ($type || $action) {
    $as = 'button';
}

$external ??= $attributes->get('target') === '_blank';

@endphp
<tk:tooltip.wrapper :$attributes :$tooltip>
    <{{ $as }} {{ $attributes
        ->dataKey($name)
        ->merge([TALLKit::dataKey(name: $name . '-has-icon') => !!$icon && $name])
        ->whereDoesntStartWith(['tooltip:', 'icon-wrapper:', 'icon:', 'icon-dot:', 'content:', 'prefix:', 'suffix:', 'icon-trailing:', 'info:', 'badge:', 'kbd:'])
        ->when($current && $as !== 'p' && $as !== 'span', fn ($attrs, $value) => $attrs->merge([
            'data-current' => $value,
            'aria-current' => $value === true ? 'page' : $value,
        ]))
        ->when($as !== 'p' || $icon, fn ($attrs) => $attrs->classes('
            [:where(&)]:inline-flex
            [:where(&)]:justify-center
            [:where(&)]:items-center
            [:where(&)]:gap-2
        '))
        ->when($as === 'a', fn ($attrs) => $attrs->merge([
            'target' => $external === true ? '_blank' : $external,
            'wire:navigate' => !$external && $navigate !== false,
            'href' => $href,
        ]))
        ->when($as === 'button', fn ($attrs) => $attrs->merge([
            'type' => $type ?? 'button',
            'wire:click' => $action,
        ], false))
        ->when($ariaLabel, fn ($attrs, $value) => $attrs->merge(['aria-label' => __($value)]))
    }}>
        {{ $prepend ?? '' }}

        @if ($icon && $iconDot)
            <span {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-wrapper:')->classes('relative') }}>
                @if (is_string($icon) && $icon !== '')
                    <tk:icon
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')"
                        :$icon
                    />
                @else
                    <tk:element
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')"
                        :label="$icon"
                    />
                @endif

                @if ($iconDot)
                    <span class="absolute -top-2 end-.5">
                        <tk:element
                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-dot:')->classes([
                                'rounded-full bg-zinc-500 dark:bg-zinc-400 size-2',
                                '
                                    flex items-center justify-center
                                    text-white tracking-tighter font-bold
                                    text-[11px] size-4
                                ' => is_string($iconDot) && strlen($iconDot) <= 2,
                            ])"
                            :label="is_string($iconDot) && strlen($iconDot) <= 2 ? $iconDot : null"
                        />
                    </span>
                @endif
            </span>
        @elseif ($icon)
            @if (is_string($icon) && $icon !== '')
                <tk:icon
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')"
                    :$icon
                />
            @else
                <tk:element
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')"
                    :label="$icon"
                />
            @endif
        @else
            {{ $iconEmpty ?? '' }}
        @endif

        @if (isset($prefix) && $prefix !== '')
            <tk:element
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'prefix:')
                    ->classes(
                        '
                            [:where(&)]:me-auto
                            [:where(&)]:font-medium
                            [:where(&)]:text-xs
                            [:where(&)]:text-zinc-500
                            dark:[:where(&)]:text-zinc-400
                        '
                    )
                "
                :label="$prefix"
            />
        @endif

        @if (TALLKit::attributesAfter(attributes: $attributes, prefix: 'content:')->isNotEmpty() && ($slot->hasActualContent() || $label))
            <tk:element
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'content:')"
                :$label
            >
                {{ $slot }}
            </tk:element>
        @elseif ($slot->hasActualContent() || $label === true)
            {{ $slot }}
        @elseif (TALLKit::isSlot(slot: $label))
            {{ $label }}
        @elseif (is_string($label))
            @php($translatedLabel = e(__($label)))

            {!! str_contains($translatedLabel, "\n") ? nl2br($translatedLabel) : $translatedLabel !!}
        @else
            {!! e(__($label)) !!}
        @endif

        @if (isset($suffix) && $suffix !== '')
            <tk:element
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'suffix:')
                    ->classes(
                        '
                            [:where(&)]:ms-auto
                            [:where(&)]:font-medium
                            [:where(&)]:text-xs
                            [:where(&)]:text-zinc-500
                            dark:[:where(&)]:text-zinc-400
                        '
                    )
                "
                :label="$suffix"
            />
        @endif

        @if ((is_string($iconTrailing) && $iconTrailing !== '') || $info)
            <tk:icon
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-trailing:')->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'info:')->getAttributes())"
                :icon="$info ? 'help' : $iconTrailing"
                :tooltip="$info"
            />
        @elseif ($iconTrailing)
            <tk:element
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-trailing:')->classes('[:where(&)]:ms-auto')"
                :label="$iconTrailing"
            />
        @else
            {{ $iconTrailingEmpty ?? '' }}
        @endif

        @if (isset($badge) && $badge !== '')
            <tk:badge
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'badge:')->classes('[:where(&)]:ms-auto')"
                :label="$badge"
            />
        @endif

        @if (isset($kbd) && $kbd !== '')
            <tk:kbd
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'kbd:')->classes('[:where(&)]:ms-auto')"
                :label="$kbd"
            />
        @endif

        {{ $append ?? '' }}
    </{{ $as }}>
</tk:tooltip.wrapper>

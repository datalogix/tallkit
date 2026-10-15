@props([
    'kind' => null,
    'href' => null,
    'external' => null,
    'route' => null,
    'parameters' => null,
    'navigate' => null,
    'action' => null,
    'as' => null,
    'type' => null,
    'exact' => null,
    'matchQuery' => null,
    'current' => null,
    'iconDot' => null,
    'ariaLabel' => null,
    'tooltip' => null,
    'label' => null,
    'icon' => null,
    'prefix' => null,
    'suffix' => null,
    'iconTrailing' => null,
    'info' => null,
    'badge' => null,
    'prepend' => null,
    'append' => null,
    'kbd' => null,
])
@php

$as ??= 'span';
$href ??= route_detect($route, $parameters, $href);
$href = TALLKit::safeHref($href);
$ariaLabel = $ariaLabel === true || $ariaLabel === null
    ? (in_array($as, ['a', 'button'], true) || $href || $type || $action || $attributes->has('role')
        ? string_value($label, $slot->hasActualContent() ? null : $tooltip)
        : null)
    : $ariaLabel;
$current ??= TALLKit::isCurrentHref($href, $exact, $matchQuery);

if ($href) {
    $as = 'a';
} elseif ($type || $action) {
    $as = 'button';
}

$external ??= $attributes->get('target') === '_blank';

$linksToFile = $href && preg_match('/\.(?!html?$|php$)[a-z0-9]{2,5}$/i', (string) parse_url((string) $href, PHP_URL_PATH));

$isDisabledLink = $as === 'a' && $attributes->enabled('disabled');

if ($isDisabledLink) {
    $attributes = $attributes->except('disabled')->merge(['role' => 'link', 'aria-disabled' => 'true']);
}

$hasIcon = has_content($icon);
$iconDotLabel = (is_string($iconDot) || is_int($iconDot)) && strlen((string) $iconDot) <= 2 ? (string) $iconDot : null;
$hasInfo = has_content($info);
$tip = TALLKit::tooltip($tooltip, $attributes, name: $ariaLabel ? __($ariaLabel) : (is_string($label) && $label !== '' ? __($label) : strip_tags((string) $slot)));

@endphp
<{{ $as }} {{ $attributes
    ->dataKey($kind)
    ->dataKey($kind ? $kind.'-has-icon' : null, $hasIcon)
    ->whereDoesntStartWith(['tooltip:', 'icon-wrapper:', 'icon:', 'icon-dot:', 'content:', 'prefix:', 'suffix:', 'icon-trailing:', 'info:', 'badge:', 'kbd:'])
    ->when($current && $as !== 'p' && $as !== 'span', fn ($attrs) => $attrs->merge([
        'data-current' => true,
        'aria-current' => $current === true ? 'page' : $current,
    ]))
    ->classes('[:where(&)]:[overflow-wrap:anywhere]')
    ->when($as !== 'p' || $hasIcon, fn ($attrs) => $attrs->classes('
        [:where(&)]:inline-flex
        [:where(&)]:justify-center
        [:where(&)]:items-center
        [:where(&)]:gap-2
    '))
    ->when($as === 'a' && ! $isDisabledLink, fn ($attrs) => $attrs->merge([
        'target' => $external === true ? '_blank' : $external,
        'wire:navigate' => ! $external && $navigate !== false && ($navigate === true || ! $linksToFile),
        'href' => $href,
    ]))
    ->when($as === 'button', fn ($attrs) => $attrs->merge([
        'type' => $type ?? 'button',
        'wire:click' => $action,
    ]))
    ->when(TALLKit::isSlot($label), fn ($attrs) => $attrs->merge($label->attributes->getAttributes()))
    ->when($ariaLabel, fn ($attrs, $value) => $attrs->merge(['aria-label' => __($value)]))
    ->merge($tip['attributes'])
    ->when($tip['describedBy'], fn ($attrs, $id) => TALLKit::withDescribedBy($attrs, $id))
}}>
    @if ($tip['html'])
        <template {{ TALLKit::dataKey('tooltip-content') }}>{!! $tip['html'] !!}</template>
    @endif

    {{ $prepend ?? '' }}

    @if ($hasIcon && has_content($iconDot))
        <span {{ $attributes->prefixed('icon-wrapper:')->classes('relative') }}>
            @if (is_string($icon) && $icon !== '')
                <tk:icon
                    :attributes="$attributes->prefixed('icon:')"
                    :$icon
                />
            @else
                <tk:element
                    :attributes="$attributes->prefixed('icon:')->except('size')"
                    :label="$icon"
                />
            @endif

            @if (has_content($iconDot))
                <span class="absolute -top-2 end-.5">
                    <tk:element
                        :attributes="$attributes->prefixed('icon-dot:')->classes([
                            'rounded-full bg-zinc-500 dark:bg-zinc-400 size-2',
                            '
                                flex items-center justify-center
                                text-white tracking-tighter font-bold
                                text-[11px] size-4
                            ' => $iconDotLabel !== null,
                        ])"
                        :label="$iconDotLabel"
                    />
                </span>
            @endif
        </span>
    @elseif ($hasIcon)
        @if (is_string($icon) && $icon !== '')
            <tk:icon
                :attributes="$attributes->prefixed('icon:')"
                :$icon
            />
        @else
            <tk:element
                :attributes="$attributes->prefixed('icon:')->except('size')"
                :label="$icon"
            />
        @endif
    @else
        {{ $iconEmpty ?? '' }}
    @endif

    @if (has_content($prefix))
        <tk:element
            :attributes="$attributes->prefixed('prefix:')
                ->classes(
                    '
                        [:where(&)]:me-auto
                        [:where(&)]:font-medium
                        [:where(&)]:text-xs
                    ',
                    TALLKit::textNeutral(variant: 'subtle', prefix: '[:where(&)]:'),
                )
            "
            :label="$prefix"
        />
    @endif

    @if ($attributes->prefixed('content:')->isNotEmpty() && ($slot->hasActualContent() || has_content($label)))
        <tk:element
            :attributes="$attributes->prefixed('content:')"
            :$label
        >
            {{ $slot }}
        </tk:element>
    @elseif ($slot->hasActualContent() || $label === true)
        {{ $slot }}
    @elseif (TALLKit::isSlot(slot: $label))
        {{ $label }}
    @elseif ($label instanceof \Illuminate\Contracts\Support\Htmlable)
        {!! $label->toHtml() !!}
    @elseif (is_string($label))
        @php($translatedLabel = e(__($label)))

        {!! str_contains($translatedLabel, "\n") ? nl2br($translatedLabel) : $translatedLabel !!}
    @else
        {!! e(__($label)) !!}
    @endif

    @if (has_content($suffix))
        <tk:element
            :attributes="$attributes->prefixed('suffix:')
                ->classes(
                    '
                        [:where(&)]:ms-auto
                        [:where(&)]:font-medium
                        [:where(&)]:text-xs
                    ',
                    TALLKit::textNeutral(variant: 'subtle', prefix: '[:where(&)]:'),
                )
            "
            :label="$suffix"
        />
    @endif

    @if ((is_string($iconTrailing) && $iconTrailing !== '') || $hasInfo)
        <tk:icon
            :attributes="TALLKit::attributesMerge($attributes->prefixed('icon-trailing:'), $attributes->prefixed('info:'))
                ->merge($hasInfo && in_array($as, ['a', 'button'], true) ? ['tabindex' => null] : [])"
            :icon="$hasInfo ? 'help' : $iconTrailing"
            :tooltip="$info"
        />
    @elseif (has_content($iconTrailing))
        <tk:element
            :attributes="$attributes->prefixed('icon-trailing:')->classes('[:where(&)]:ms-auto')"
            :label="$iconTrailing"
        />
    @else
        {{ $iconTrailingEmpty ?? '' }}
    @endif

    @if (has_content($badge))
        <tk:badge
            :attributes="$attributes->prefixed('badge:')->classes('[:where(&)]:ms-auto')"
            :label="$badge"
        />
    @endif

    @if (has_content($kbd))
        <tk:kbd
            :attributes="$attributes->prefixed('kbd:')->classes('[:where(&)]:ms-auto')"
            :label="$kbd"
        />
    @endif

    {{ $append ?? '' }}
</{{ $as }}>

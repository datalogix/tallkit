@props([
    'name' => null,
    'icon' => null,
    'size' => null,
    'image' => null,
    'svg' => null,
    'tooltip' => null,
])
@php

$iconName = $name ?? $icon;

if (Str::isUrl($iconName)) {
    $image ??= $iconName;
} else {
    $svg ??= TALLKit::iconSvg(name: $iconName);
}

$named = $attributes->has('aria-label') || $attributes->has('aria-labelledby');
$tip = TALLKit::tooltip($tooltip, $attributes, name: $named ? $attributes->get('aria-label') : null, isName: ! $named);
$tipText = $tip['attributes'][TALLKit::dataKey('tooltip')] ?? null;
$isDecorative = ! $tipText && ! $named;
$ariaLabel = ! $named ? $tipText : null;
$focusable = $tipText && ! $attributes->has('tabindex')
    ? ['tabindex' => '0', 'class' => 'rounded-sm outline-offset-2 focus-visible:outline-2 focus-visible:outline-current']
    : [];
$own = fn ($attrs) => TALLKit::withTooltip($attrs, $tip, describe: (bool) $named)->merge($focusable);

@endphp
@if ($image || $svg || $slot->isNotEmpty())
    @if ($image)
        <img
            src="{{ $image }}"
            {{
                $own($attributes
                    ->dataKey('icon')
                    ->classes('object-cover rounded', TALLKit::widthHeight(size: $size))
                    ->when($isDecorative, fn ($attrs) => $attrs->merge(['aria-hidden' => 'true', 'alt' => '']))
                    ->when($ariaLabel, fn ($attrs, $value) => $attrs->merge(['aria-label' => $value])))
            }}
        />
    @elseif($svg)
        {!! Str::of($svg)->replaceFirst('<svg', '<svg '
            .($isDecorative ? 'aria-hidden="true" focusable="false" ' : 'role="img" ')
            .$own($attributes->dataKey('icon')->classes('text-current', TALLKit::widthHeight(size: $size))
                ->when($ariaLabel, fn ($attrs, $value) => $attrs->merge(['aria-label' => $value])))) !!}
    @elseif ($tipText)
        <span {{ $own($attributes->dataKey('icon')->merge(['role' => 'img', 'aria-label' => $ariaLabel])->classes('inline-flex')) }}>{{ $slot }}</span>
    @else
        {{ $slot }}
    @endif
@endif

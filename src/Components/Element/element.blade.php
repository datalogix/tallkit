@php
$external ??= $attributes->get('target') === '_blank';
@endphp

<tk:tooltip.wrapper :$attributes :$tooltip>
    <{{ $as }} {{ $attributes
        ->whereDoesntStartWith(['tooltip:', 'icon-wrapper:', 'icon:', 'icon-dot:', 'content:', 'prefix:', 'suffix:', 'icon-trailing:', 'info:', 'badge:', 'kbd:'])
        ->when($as !== 'p' || $icon, fn ($attrs) => $attrs->classes('inline-flex items-center gap-1.5'))
        ->when($as === 'a', fn ($attrs) => $attrs->merge([
            'target' => $external === true ? '_blank' : $external,
            'wire:navigate' => !$external && $navigate !== false,
            'href' => $href,
            'data-current' => $current ?? is_current_href($href, $exact),
        ]))
        ->when($as === 'button', fn ($attrs) => $attrs->merge([
            'type' => $type ?? 'button',
            'data-current' => $current,
            'wire:click' => $action,
        ], false))
        ->when($ariaLabel, fn ($attrs, $value) => $attrs->merge(['aria-label' => __($value)]))
        ->merge([$dataKey('has-icon') => !!$icon && $name])
    }}>
        {{ $prepend ?? '' }}

        @if ($icon && $iconDot)
            <span {{ $attributesAfter('icon-wrapper:')->classes('relative') }}>
                @if (is_string($icon) && $icon !== '')
                    <tk:icon
                        :attributes="$attributesAfter('icon:')"
                        :$icon
                    />
                @else
                    <tk:element
                        :attributes="$attributesAfter('icon:')"
                        :label="$icon"
                    />
                @endif

                @if ($iconDot)
                    <span class="absolute -top-2 end-.5">
                        <tk:element
                            :attributes="$attributesAfter('icon-dot:')->classes([
                                'rounded-full bg-zinc-500 dark:bg-zinc-400 size-2',
                                '
                                    flex items-center justify-center
                                    text-white tracking-tighter font-bold
                                    text-[11px] size-4.5
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
                    :attributes="$attributesAfter('icon:')"
                    :$icon
                />
            @else
                <tk:element
                    :attributes="$attributesAfter('icon:')"
                    :label="$icon"
                />
            @endif
        @else
            {{ $iconEmpty ?? '' }}
        @endif

        @if (isset($prefix) && $prefix !== '')
            <tk:element
                :attributes="$attributesAfter('prefix:')->classes('me-auto font-medium text-xs text-zinc-500 dark:text-zinc-400')"
                :label="$prefix"
            />
        @endif

        @if (count($attributesAfter('content:')->toArray()) > 1 && ($slot->hasActualContent() || $label))
            <tk:element
                :attributes="$attributesAfter('content:')"
                :$label
            >
                {{ $slot }}
            </tk:element>
        @else
            {!! $slot->hasActualContent() || $label === true ? $slot : ($isSlot($label) ? $label : __($label)) !!}
        @endif

        @if (isset($suffix) && $suffix !== '')
            <tk:element
                :attributes="$attributesAfter('suffix:')->classes('ms-auto font-medium text-xs text-zinc-500 dark:text-zinc-400')"
                :label="$suffix"
            />
        @endif

        @if ((is_string($iconTrailing) && $iconTrailing !== '') || $info)
            <tk:icon
                :attributes="$attributesAfter('icon-trailing:')->merge($attributesAfter('info:')->getAttributes())"
                :icon="$info ? 'help' : $iconTrailing"
                :tooltip="$info"
            />
        @elseif ($iconTrailing)
            <tk:element
                :attributes="$attributesAfter('icon-trailing:')->classes('ms-auto')"
                :label="$iconTrailing"
            />
        @else
            {{ $iconTrailingEmpty ?? '' }}
        @endif

        @if (isset($badge) && $badge !== '')
            <tk:badge
                :attributes="$attributesAfter('badge:')->classes('ms-auto')"
                :label="$badge"
            />
        @endif

         @if (isset($kbd) && $kbd !== '')
            <tk:kbd
                :attributes="$attributesAfter('kbd:')"
                :label="$kbd"
            />
        @endif

        {{ $append ?? '' }}
    </{{ $as }}>
</tk:tooltip.wrapper>

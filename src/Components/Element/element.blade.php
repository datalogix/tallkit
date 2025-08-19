<tk:tooltip.wrapper :$attributes>
    <{{ $as }} {{ $attributes
        ->whereDoesntStartWith(['tooltip:', 'icon-wrapper:', 'icon:', 'icon-dot:', 'content:', 'suffix:', 'icon-trailing:', 'badge:', 'information:'])
        ->when($as !== 'p', fn ($attrs) => $attrs->classes('flex items-center gap-1.5'))
        ->when($as === 'a', fn ($attrs) => $attrs->merge(['wire:navigate' => $navigate !== false, 'href' => $href, 'data-current' => $current ?? is_current_href($href)]))
        ->when($as === 'button', fn ($attrs) => $attrs->merge(['type' => $type ?? 'button', 'data-current' => $current, 'wire:click' => $action]))
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
                    <span class="absolute top-[-1px] end-[-1px]">
                        <tk:element :attributes="$attributesAfter('icon-dot:')->classes('size-[6px] rounded-full bg-zinc-500 dark:bg-zinc-400')" />
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

        @if (count($attributesAfter('content:')->toArray()) > 1 && ($slot->isNotEmpty() || $label))
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
                :attributes="$attributesAfter('suffix:')->classes('text-xs text-zinc-500 dark:text-zinc-400')"
                :label="$suffix"
            />
        @endif

        @if ((is_string($iconTrailing) && $iconTrailing !== '') || $information)
            <tk:icon
                :attributes="$attributesAfter('icon-trailing:')->merge($attributesAfter('information:')->getAttributes())"
                :icon="$information ? 'help' : $iconTrailing"
                :tooltip="$information"
            />
        @elseif ($iconTrailing)
            <tk:element
                :attributes="$attributesAfter('icon-trailing:')"
                :label="$iconTrailing"
            />
        @else
            {{ $iconTrailingEmpty ?? '' }}
        @endif

        @if (isset($badge) && $badge !== '')
            <tk:badge
                :attributes="$attributesAfter('badge:')"
                :label="$badge"
            />
        @endif

        {{ $append ?? '' }}
    </{{ $as }}>
</tk:tooltip.wrapper>

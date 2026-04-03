@php
$message ??= $getError(name: $name, slot: $slot);
@endphp

@if ($message)
    <div {{
        $attributes
            ->whereDoesntStartWith(['icon:'])
            ->classes(
                'flex items-center text-red-500 dark:text-red-400',
                $fontSize(size: $size),
                $iconSize(size: $size),
                $gap(size: $size),
            )
        }}
        role="alert"
        aria-live="polite"
        aria-atomic="true"
    >
        @if ($message && $icon !== false)
            <tk:icon
                :attributes="$attributesAfter('icon:')"
                :name="is_string($icon) ? $icon : 'alert-outline'"
            />
        @endif

        {!! $message !!}
    </div>
@endif

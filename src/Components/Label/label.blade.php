@php
$hasPrependOrAppend = $labelPrepend || $labelAppend;
$textClasses = $classes(
    '[:where(&)]:text-zinc-800 dark:[:where(&)]:text-white',
    match ($size) {
        'xs' => 'text-[11px] font-normal',
        'sm' => 'text-xs font-medium',
        default => 'text-sm font-medium',
        'lg' => 'text-base font-semibold',
        'xl' => 'text-lg font-semibold',
        '2xl' => 'text-xl font-bold',
        '3xl' => 'text-2xl font-bold',
    }
)
@endphp

@if ($slot->isNotEmpty() || $label)
    @if ($hasPrependOrAppend)
        <div
            {{ $dataKey() }}
            {{ $attributesAfter('area:')->classes('flex items-center gap-4', $textClasses) }}
        >
    @endif

    @if ($labelPrepend)
        <div {{ $attributesAfter('label-prepend:')->classes('mr-auto') }}>
            {{ $labelPrepend }}
        </div>
    @endif

    <{{ $as ?? ($for ? 'label' : 'span') }}
        x-data="label"
        {{ $attributesAfter('container:')
            ->classes(['inline-flex', 'flex-1' => $hasPrependOrAppend])
            ->merge([$dataKey() => $hasPrependOrAppend ? false : ''])
        }}
        @if ($for && $as !== 'label') for="{{ $for }}" @endif
    >
        <tk:element
            :$label
            :icon:size="$adjustSize()"
            :icon-trailing:size="$adjustSize()"
            :badge:size="$adjustSize()"
            :attributes="$attributes->whereDoesntStartWith(['area:', 'label-prepend:', 'label-append:', 'container:', 'information:', $dataKey()])
                ->merge($attributesAfter('information:', prepend: 'icon-trailing:')->getAttributes())
                ->classes($textClasses)
            "
        >
            {{ $slot }}
        </tk:element>
    </{{ $as ?? 'label'}}>

    @if ($labelAppend)
        <div {{ $attributesAfter('label-append:')->classes('ml-auto') }}>
            {{ $labelAppend }}
        </div>
    @endif

    @if ($hasPrependOrAppend)
        </div>
    @endif
@endif

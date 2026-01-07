@php
$hasPrependOrAppend = $labelPrepend || $labelAppend;
@endphp

@if ($slot->hasActualContent() || $label)
    @if ($hasPrependOrAppend)
        <div
            {{ $dataKey() }}
            {{
                $attributesAfter('area:')->classes(
                    'flex items-center gap-4',
                    '[:where(&)]:text-zinc-800 dark:[:where(&)]:text-white',
                    $fontSize($size, true)
                )
            }}
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
            ->classes([
                'inline-flex',
                'flex-1' => $hasPrependOrAppend,
                'sr-only' => $srOnly
            ])
            ->merge([$dataKey() => $hasPrependOrAppend ? false : ''])
        }}
        @if ($for && $as !== 'label') for="{{ $for }}" @endif
    >
        <tk:element
            :$label
            :icon:size="$adjustSize()"
            :icon-trailing:size="$adjustSize()"
            :badge:size="$adjustSize()"
            :attributes="$attributes->whereDoesntStartWith(['area:', 'label-prepend:', 'label-append:', 'container:', 'info:', $dataKey()])
                ->merge($attributesAfter('info:', prepend: 'icon-trailing:')->getAttributes())
                ->classes('[:where(&)]:text-zinc-800 dark:[:where(&)]:text-white', $fontSize($size, true))
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

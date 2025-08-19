@php
$hasPrependOrAppend = $labelPrepend || $labelAppend;
@endphp

@if ($slot->isNotEmpty() || $label)
    @if ($hasPrependOrAppend)
        <div
            {{ $dataKey() }}
            {{ $attributesAfter('area:')->classes('flex items-center gap-4') }}
        >
            {{ $labelPrepend }}
    @endif

    <{{ $as ?? 'label'}}
        x-data="label"
        {{ $attributesAfter('container:')
            ->classes(['inline-flex', 'flex-1' => $hasPrependOrAppend])
            ->merge([$dataKey() => ! $hasPrependOrAppend])
        }}
        @isset ($for) for="{{ $for }}" @endisset
    >
        <tk:element
            :name="$baseComponentKey()"
            :$label
            :icon:size="$adjustSize()"
            :icon-trailing:size="$adjustSize()"
            :badge:size="$adjustSize()"
            :attributes="$attributes->whereDoesntStartWith(['area:', 'container:', 'information:'])
                ->merge($attributesAfter('information:', prepend: 'icon-trailing:')->getAttributes())
                ->classes(
                    '[:where(&)]:text-zinc-800 [:where(&)]:dark:text-white',
                    match ($size) {
                        'xs' => 'text-xs font-normal',
                        'sm' => 'text-sm font-medium',
                        default => 'text-base font-medium',
                        'lg' => 'text-lg font-semibold',
                        'xl' => 'text-xl font-semibold',
                        '2xl' => 'text-2xl font-bold',
                        '3xl' => 'text-3xl font-bold',
                    },
                )
            "
        >
            {{ $slot }}
        </tk:element>
    </{{ $as ?? 'label'}}>

    @if ($hasPrependOrAppend)
            {{ $labelAppend }}
        </div>
    @endif
@endif

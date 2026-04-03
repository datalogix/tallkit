@props([
    'silent' => null,
])
<div
    wire:ignore
    x-cloak
    x-data="{{ $attributes->pluck('x-data', 'loadable') }}"
    {{ $attributes->dataKey('loadable')->whereDoesntStartWith(['loading:', 'error:']) }}
    @if ($silent) data-silent @endif
>
    @isset ($empty)
        <template x-if="isEmpty()">
            {{ $empty }}
        </template>
    @endisset

    <template x-if="isCompleted()">
        @isset ($completed)
            {{ $completed }}
        @else
            {{ $slot }}
        @endisset
    </template>

    <template x-if="isLoading()">
        @isset ($loading)
            {{ $loading }}
        @else
            <tk:loading :attributes="TALLKit::attributesAfter($attributes, 'loading:')" />
        @endisset
    </template>

    <template x-if="isError()">
        @isset ($error)
            {{ $error }}
        @else
            <tk:error :attributes="TALLKit::attributesAfter($attributes, 'error:')">
                <span x-text="error"></span>
            </tk:error>
        @endisset
    </template>
</div>

@props([
    'silent' => null,
])
<div
    wire:ignore
    x-cloak
    x-data="{{ $attributes->pluck('x-data', 'loadable') }}"
    {{
        $attributes
            ->whereDoesntStartWith(['loading:', 'error:', 'empty:'])
            ->dataKey('loadable')
            ->merge(['data-silent' => (bool) $silent])
    }}
>
    <template x-if="isEmpty()">
        @isset ($empty)
            {{ $empty }}
        @else
            <tk:text
                :attributes="TALLKit::attributesAfter($attributes, 'empty:')"
                variant="subtle"
                label="Nothing to show"
            />
        @endisset
    </template>

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
                <span x-text="error?.message ?? error"></span>
            </tk:error>
        @endisset
    </template>
</div>

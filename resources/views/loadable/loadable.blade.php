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
                :attributes="$attributes->prefixed('empty:')->merge(['label' => 'Nothing to show'])"
                variant="subtle"
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
            <tk:loading :attributes="$attributes->prefixed('loading:')" />
        @endisset
    </template>

    <template x-if="isError()">
        @isset ($error)
            {{ $error }}
        @else
            <tk:error :attributes="$attributes->prefixed('error:')">
                <span x-text="error?.message ?? error"></span>
            </tk:error>
        @endisset
    </template>
</div>

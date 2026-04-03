<div
    wire:ignore
    x-cloak
    x-data="{{ $attributes->pluck('x-data', 'loadable') }}"
    {{ $attributes->whereDoesntStartWith(['loading:', 'error:']) }}
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
            <tk:loading :attributes="$attributesAfter('loading:')" />
        @endisset
    </template>

    <template x-if="isError()">
        @isset ($error)
            {{ $error }}
        @else
            <tk:error :attributes="$attributesAfter('error:')">
                <span x-text="error"></span>
            </tk:error>
        @endisset
    </template>
</div>

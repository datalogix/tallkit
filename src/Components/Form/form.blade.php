<form {{
    $attributes->whereDoesntStartWith(['error-group:', 'submit:'])
        ->classes(
            '[:where(&)]:space-y-6',
            match ($errorGroup) {
                'only' => '[&_[data-tallkit-error]]:hidden',
                default => ''
            }
        )
        ->when(
            in_livewire(),
            fn ($attrs) => $attrs->merge(['wire:submit' => $action]),
            fn ($attrs) => $attrs
                ->merge(!$enctype && Str::contains($slot, 'type="file"', true) ? ['enctype' => 'multipart/form-data'] : [])
                ->merge(['method' => $method])
                ->merge(['action' => $action])
        )
}}>
    @unless (in_livewire())
        @unless (in_array($method, ['HEAD', 'GET', 'OPTIONS']))
            @csrf
        @endunless

        @if ($spoofMethod)
            @method($method)
        @endif
    @endunless

    @if ($errorGroup)
        <tk:error.group :attributes="$attributesAfter('error-group:')" />
    @endif

    {{ $slot }}

    @if ($action && Str::doesntContain($slot, 'type="submit"', true))
        <tk:submit
            :attributes="$attributesAfter('submit:')->classes('w-full')"
            variant="inverse"
        />
    @endif
</form>

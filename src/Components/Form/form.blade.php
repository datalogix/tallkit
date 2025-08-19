<form {{
    $attributes->whereDoesntStartWith(['submit:'])->classes('space-y-6')->when(
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

    {{ $slot }}

    @if ($action && Str::doesntContain($slot, 'type="submit"', true))
        <tk:submit :attributes="$attributesAfter('submit:')->classes('w-full')" />
    @endif
</form>

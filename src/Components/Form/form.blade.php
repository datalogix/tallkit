<form {{ $attributes
    ->merge(!$enctype && str_contains($slot, 'type="file"') ? ['enctype' => 'multipart/form-data'] : [])
    ->merge(['method' => $method])
}}>
    @unless(in_array($method, ['HEAD', 'GET', 'OPTIONS']))
        @csrf
    @endunless

    @if ($spoofMethod)
        @method($method)
    @endif

    {{ $slot }}
</form>

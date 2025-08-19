<option
    {{ $attributes }}
    @selected($selected)
    @isset($value) value="{{ $value }}" @endisset
>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @elseif (is_array($label) || is_object($label))
        @json($label)
    @else
        {{ __($label) }}
    @endif
</option>

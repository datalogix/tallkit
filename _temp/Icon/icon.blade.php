<tk:tooltip.wrapper :$attributes>
    @if ($image)
        <img src="{{ $image }}" {{ $attributes->classes('object-cover rounded', $widthHeight($size)) }} />
    @else
        {!! Str::of($svg)->replace('<svg', '<svg '.$attributes->classes('text-current', $widthHeight($size))) !!}
    @endif
</tk:tooltip.wrapper>

<tk:input
    :$attributes
    :$placeholder
    :prefix="$position === 'prefix' ? $symbol : null"
    :suffix="$position === 'suffix' ? $symbol : null"
    x-mask:dynamic="$money($input, '{{ $delimiter }}', '{{ $thousands }}')"
/>

@props([
    'underline' => null,
])
<tk:text
    name="link"
    as="a"
    :attributes="$attributes->classes(
        match ($underline) {
            true => 'underline hover:no-underline',
            default => 'no-underline hover:underline',
        }
    )"
>
    {{ $slot }}
</tk:text>

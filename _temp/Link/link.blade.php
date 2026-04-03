<tk:text
    as="a"
    :name="$baseComponentKey()"
    :attributes="$attributes->classes(
        match ($underline) {
            true => 'underline hover:no-underline',
            default => 'no-underline hover:underline',
        }
    )"
>
    {{ $slot }}
</tk:text>

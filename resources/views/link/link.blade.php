@props([
    'underline' => null,
])
<tk:text
    name="link"
    :attributes="$attributes
        ->classes(
            match ((bool) $underline) {
            true => 'underline hover:no-underline',
            default => 'no-underline hover:underline',
        })
        ->when(
            $attributes->get('target') === '_blank' && ! $attributes->has('rel'),
            fn ($attrs) => $attrs->merge(['rel' => 'noopener noreferrer']),
        )
    "
>
    {{ $slot }}
</tk:text>

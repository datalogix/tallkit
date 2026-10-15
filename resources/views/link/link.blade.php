@props([
    'underline' => null,
    ...TALLKit::elementProps(),
])
<tk:text
    kind="link"
    :attributes="$attributes->withElementProps(get_defined_vars())
        ->classes(
            match ($underline !== false) {
            true => 'underline underline-offset-2 hover:no-underline',
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

@props([
    'underline' => null,
    ...TALLKit::elementProps(),
])
<tk:text
    kind="link"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())
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

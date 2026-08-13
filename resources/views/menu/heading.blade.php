@aware(['size'])
@props([
    'label' => null,
    'size' => null,
])
<tk:heading
    :attributes="$attributes->classes(
        'w-full justify-start text-zinc-500 dark:text-zinc-400',
        TALLKit::padding(size: $size, mode: 'smallest'
    ))"
    :size="TALLKit::adjustSize(size: $size)"
    :$label
>
    {{ $slot }}
</tk:heading>

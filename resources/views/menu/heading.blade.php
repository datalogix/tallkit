@props([
    'label' => null,
    'size' => null,
])
<div
    {{
        $attributes
            ->whereDoesntStartWith(['container:', 'has-icon:'])
            ->classes('p-2 pb-1 w-full flex items-center text-start')
    }}
>
    <div {{ TALLKit::attributesAfter($attributes, 'has-icon:')
        ->classes('w-8.5 hidden [[data-tallkit-menu]:has(>[data-tallkit-menu-item-has-icon])_&]:block')
    }}></div>

    <tk:heading
        :attributes="TALLKit::attributesAfter($attributes, 'container:')->classes('text-zinc-500 dark:text-zinc-400')"
        :size="TALLKit::adjustSize(size: $size)"
        :$label
    >
        {{ $slot }}
    </tk:heading>
</div>

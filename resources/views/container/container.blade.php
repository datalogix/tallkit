@props([
    'size' => null,
])
<div
    {{
        $attributes
            ->dataKey('container')
            ->classes('mx-auto w-full px-4 lg:px-6', TALLKit::containerWidth($size))
    }}
>
    {{ $slot }}
</div>

@props(['size' => null])
<div
    aria-hidden="true"
    {{
        $attributes->classes(
            $size
                ? 'shrink-0 '.TALLKit::generateClassBySize(size: $size, name: 'size', values: ['2', '3', '4', '6', '8', '10', '12'])
                : 'flex-1'
        )
    }}
></div>

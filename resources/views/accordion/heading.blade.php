@aware(['size', 'collapse', 'reversed', 'border', 'disabled', 'level'])
@props([
    'size' => null,
    'collapse' => null,
    'reversed' => null,
    'border' => null,
    'disabled' => null,
    'label' => null,
    'level' => 2,
])
@if ($level)<div role="heading" aria-level="{{ $level }}">@endif
<tk:button
    :attributes="$attributes->classes(
        TALLKit::paddingInline(size: $border ? $size : 'none', mode: 'largest'),
        TALLKit::paddingBlock(size: $size, mode: 'largest'),
        'w-full [&_[data-tallkit-icon]]:ml-auto',
    )"
    :$size
    :$disabled
    :$label
    variant="none"
    content:class="flex-1 justify-start"
    :icon="$reversed ? 'chevron-right' : false"
    icon:class="rtl:-scale-x-100"
    icon::class="{ 'transition': {{ $collapse !== false ? 'true' : 'false' }}, 'rotate-90 rtl:-rotate-90': opened }"
    :iconTrailing="$reversed ? false : 'chevron-down'"
    icon-trailing::class="{ 'transition': {{ $collapse !== false ? 'true' : 'false' }}, 'rotate-180': opened }"
>
    {{ $slot }}
</tk:button>
@if ($level)</div>@endif

@props([
    'type' => null,
    'icon' => null,
    'prepend' => null,
    'title' => null,
    'message' => null,
    'append' => null,
    'actions' => null,
    'border' => null,
    'dismissible' => null,
    'animation' => null,
    'timeout' => null,
    'size' => null,
])
<tk:content
    x-data="alertComponent({{ Js::from($timeout) }}, {{ Js::from($animation ?? true) }})"
    role="alert"
    :attributes="$attributes
        ->dataKey('alert')
        ->whereDoesntStartWith(['message:', 'dismissible:'])
        ->merge(TALLKit::attributesAfter($attributes, 'message:', prepend: 'description:')->getAttributes())
        ->classes(
            TALLKit::padding(size: $size),
            'mb-4 transition-all duration-300 ease-out opacity-100',
            match ($type) {
                'danger' => 'text-red-800 bg-red-100 dark:bg-zinc-700 dark:text-red-300',
                'success' => 'text-green-800 bg-green-100 dark:bg-zinc-700 dark:text-green-300',
                'warning' => 'text-yellow-800 bg-yellow-100 dark:bg-zinc-700 dark:text-yellow-300',
                'info' => 'text-blue-800 bg-blue-100 dark:bg-zinc-700 dark:text-blue-300',
                default => 'text-zinc-800 bg-zinc-100 dark:bg-zinc-700 dark:text-zinc-300',
            },
            match ($border) {
                'top' => 'border-t-3',
                'left' => 'border-l-3',
                'right' => 'border-r-3',
                'bottom' => 'border-b-3',
                true => 'border',
                default => 'border-none',
            },
            match ($border) {
                'top', 'left', 'right', 'bottom' => '',
                default => 'rounded',
            },
        )
        ->when(
            $border,
            fn ($c) => $c->classes(match ($type) {
                'danger' => 'border-red-300/50 dark:border-red-200/40',
                'success' => 'border-green-300/50 dark:border-green-200/40',
                'warning' => 'border-yellow-300/50 dark:border-yellow-200/40',
                'info' => 'border-blue-300/50 dark:border-blue-200/40',
                default => 'border-zinc-300/50 dark:border-zinc-200/40',
            })
        )
    "
    :$size
    :icon="is_string($icon) || $icon === false ? $icon : match ($type) {
        'danger' => 'close-circle-outline',
        'success' => 'check-circle-outline',
        'warning' => 'warning-outline',
        default => 'info-circle-outline',
    }"
    icon:class="shrink-0"
    :$prepend
    :$title
    title:mode="large"
    title:variant="none"
    :description="$message"
    description:variant="none"
    :$append
>
    {{ $slot }}

    @if ($actions || $dismissible)
        <x-slot:actions>
            {{ $actions }}

            @if ($dismissible)
                <tk:alert.close
                    :attributes="TALLKit::attributesAfter($attributes, 'dismissible:')"
                    :icon="is_string($dismissible) ? $dismissible : null"
                    :$type
                    :$size
                />
            @endif
        </x-slot:actions>
    @endif
</tk:content>

@aware(['dense'])

<th {{ $attributes->classes([
    'py-4 px-6' => !$dense,
    'p-2.5' => $dense,
    '[:where(&)]:font-medium [:where(&)]:text-sm',
    '[:where(&)]:text-zinc-800 [:where(&)]:dark:text-white',
]) }}>
    <tk:element
        :label="$label ?? Str::headline($name)"
        :as="$sortable ? 'button' : null"
        :attributes="$attributesAfter('container:')->classes(
            'group/sortable',
            match ($align) {
                'center' => 'text-center justify-center',
                'right' => 'text-end justify-end',
                default => 'text-start justify-start',
            })
        "
        :icon-trailing="match($sortable) {
            true => 'chevron-up-down',
            'desc' => 'chevron-down',
            'asc' => 'chevron-up',
            default => false
        }"
        :icon-trailing:class="$sortable === true ? 'opacity-50 group-hover/sortable:opacity-100' : ''"
        icon-trailing:size="xs"
    >
        {{ $slot }}
    </tk:element>
</th>

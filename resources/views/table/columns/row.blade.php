@aware(['draggable' => null])
<tr {{
    $attributes->merge($draggable ? [
        'x-sort.ghost' => 'columnSorted($el, $item)',
        'x-sort:config' => 'columnSortConfig()',
    ] : [], escape: false)
}}>
    {{ $slot }}
</tr>

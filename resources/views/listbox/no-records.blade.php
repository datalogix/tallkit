@props([
    ...TALLKit::elementProps(),
])
<tk:text
    :attributes="$attributes->withElementProps(get_defined_vars())->dataKey('listbox-no-records')"
    label="No records found"
    variant="subtle"
    hidden
    class="p-4 text-center"
    aria-live="polite"
    role="status"
>
    {{ $slot }}
</tk:text>

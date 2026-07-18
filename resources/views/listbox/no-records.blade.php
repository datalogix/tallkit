<tk:text
    :$attributes
    hidden
    class="p-4 text-center"
    aria-live="polite"
    label="No records found"
    variant="subtle"
    role="status"
>
    {{ $slot }}
</tk:text>

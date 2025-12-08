<tk:modal
    :attributes="$attributes->whereDoesntStartWith(['actions:', 'cancel:', 'confirm:'])->classes('max-w-sm')"
    :$size
    :title="$title ?? match($variant) {
        default => 'Are you sure?',
        'delete' => 'Do you really want to delete this record?',
    }"
    :subtitle="$subtitle ?? match($variant) {
        default => 'Do you really want to proceed with this action?',
        'delete' => 'This action is permanent and cannot be undone. All related data will be lost.',
    }"
>
    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    <div {{ $attributesAfter('actions:')->classes('flex items-center gap-2 mt-10') }}>
        <tk:modal.close
            :attributes="$attributesAfter('cancel:')"
            :$size
            :action="$cancel"
            label="Cancel"
        />

        <tk:button
            :attributes="$attributesAfter('confirm:')
                ->merge($autoClose === false ? [] : [$buildDataAttribute('modal-auto-close') => ''])
                ->classes('ms-auto')
            "
            :$size
            :action="$confirm ?? $variant ?? 'confirm'"
            :variant="match($variant) {
                default => 'inverse',
                'delete' => 'danger',
            }"
            label="Confirm"
        />
    </div>
</tk:modal>

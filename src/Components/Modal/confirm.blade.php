<tk:modal
    :attributes="$attributes->whereDoesntStartWith(['form:', 'actions:', 'cancel:', 'confirm:'])->classes('max-w-sm')"
    :$size
    :title="$title ?? match($variant) {
        default => 'Are you sure?',
        'delete' => 'Do you really want to delete this record?',
    }"
    :subtitle="$subtitle ?? match($variant) {
        default => null,
        'delete' => 'This action is permanent and cannot be undone. All related data will be lost.',
    }"
>
    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    <tk:form
        :attributes="$attributesAfter('form:')"
        :action="$action ?? $variant ?? 'confirm'"
    >
        <div {{ $attributesAfter('actions:')->classes('flex items-center gap-2 mt-10') }}>
            <tk:modal.close
                :attributes="$attributesAfter('cancel:')"
                :$size
                label="Cancel"
            />
            <tk:submit
                :attributes="$attributesAfter('confirm:')->classes('ms-auto')"
                :$size
                label="Confirm"
                :variant="match($variant) {
                    default => 'inverse',
                    'delete' => 'danger',
                }"
            />
        </div>
    </tk:form>
</tk:modal>

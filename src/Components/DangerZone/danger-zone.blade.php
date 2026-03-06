<tk:alert
    :attributes="$attributes->whereDoesntStartWith(['modal:', 'trigger:'])"
    title="Danger Zone"
    message="By deleting this record, all associated data will be permanently lost and cannot be recovered."
    type="danger"
>
    <x-slot:append>
        @if ($slot->isEmpty())
            <tk:modal.confirm
                :attributes="$attributesAfter('modal:')"
                variant="delete"
            >
                <tk:button
                    :attributes="$attributesAfter('trigger:')->classes('mt-4')"
                    variant="danger"
                    label="Delete"
                />
            </tk:modal.confirm>
        @else
            {{ $slot }}
        @endif
    </x-slot:append>
</tk:alert>

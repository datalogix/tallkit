<tk:alert
    :attributes="$attributes->whereDoesntStartWith(['modal:', 'trigger:'])"
    :title="$title ?? 'Danger Zone'"
    :message="$message ?? 'By deleting this record, all associated data will be permanently lost and cannot be recovered.'"
    type="danger"
>
    <x-slot:actions>
        @if ($slot->isEmpty())
            <tk:modal.confirm
                :attributes="$attributesAfter('modal:')"
                variant="delete"
            >
                <tk:button
                    :attributes="$attributesAfter('trigger:')"
                    variant="danger"
                    label="Delete"
                />
            </tk:modal.confirm>
        @else
            {{ $slot }}
        @endif
    </x-slot:actions>
</tk:alert>

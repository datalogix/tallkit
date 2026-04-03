@props([
    'title' => null,
    'message' => null,
])
<tk:alert
    :attributes="$attributes->whereDoesntStartWith(['modal:', 'trigger:'])"
    :title="$title ?? 'Danger Zone'"
    :message="$message ?? 'By deleting this record, all associated data will be permanently lost and cannot be recovered.'"
    type="danger"
>
    <x-slot:append>
        @if ($slot->isEmpty())
            <tk:modal.confirm
                :attributes="TALLKit::attributesAfter($attributes, 'modal:')"
                variant="delete"
            >
                <tk:button
                    :attributes="TALLKit::attributesAfter($attributes, 'trigger:')->classes('mt-4')"
                    variant="danger"
                    label="Delete"
                />
            </tk:modal.confirm>
        @else
            {{ $slot }}
        @endif
    </x-slot:append>
</tk:alert>

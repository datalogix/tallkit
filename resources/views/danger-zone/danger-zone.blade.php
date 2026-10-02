@props([
    'prepend' => null,
    'actions' => null,
])
@php
$titleId = ($attributes->get('id') ?? TALLKit::stableId('danger-zone')).'-title';
@endphp
<tk:alert
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['prepend' => null, 'actions' => null, 'pauseOnHover' => null])->whereDoesntStartWith(['modal:', 'trigger:'])
        ->merge([
            'title' => 'Danger Zone',
            'title:id' => $titleId,
            'aria-labelledby' => $titleId,
            'message' => 'By deleting this record, all associated data will be permanently lost and cannot be recovered.',
        ])"
    type="danger"
    role="group"
    aria-live="off"
>
    <x-slot:append>
        @if ($slot->isEmpty())
            <tk:modal.confirm
                :attributes="$attributes->prefixed('modal:')"
                variant="delete"
            >
                <tk:button
                    :attributes="$attributes->prefixed('trigger:')->classes('mt-4')->merge(['label' => 'Delete'])"
                    variant="danger"
                />
            </tk:modal.confirm>
        @else
            {{ $slot }}
        @endif
    </x-slot:append>
</tk:alert>

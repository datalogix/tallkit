@props([
    'items' => null,
    'value' => null,
    'itemKey' => 'id',
    'itemLabel' => 'name',
    'select' => null,
    'confirm' => 'confirmSimilar',
    'cancel' => 'cancelSimilar',
    'message' => null,
    'size' => null,
])
@php

$component = TALLKit::livewireComponent();
$items = collect($items ?? ($component && property_exists($component, 'similar') ? $component->similar : []));
$similar = $component?->getAttributes()->whereInstanceOf(\TALLKit\Livewire\Similar::class)->first();
$value ??= $similar?->getValue();
$updating = (bool) $similar?->getRecord()?->exists;
$id = $attributes->get('id') ?? TALLKit::stableId('similar');
$message ??= match (true) {
    ! has_content($value) => 'Do you want to continue anyway?',
    $updating => __('Do you want to save ":value" anyway?', ['value' => $value]),
    default => __('Do you want to create ":value" anyway?', ['value' => $value]),
};

@endphp
@if ($items->isNotEmpty())
    <tk:alert
        :attributes="$attributes->whereDoesntStartWith(['items:', 'item:', 'select:', 'confirm:', 'cancel:', 'actions:'])
            ->merge(['id' => $id, 'title' => 'Similar records found'])"
        type="warning"
        :$size
    >
        <ul {{ $attributes->prefixed('items:')->classes('my-2 space-y-1') }}>
            @foreach ($items as $item)
                @php($key = data_get($item, $itemKey))
                <li
                    wire:key="{{ $id }}-{{ $key }}"
                    {{ $attributes->prefixed('item:')->classes('flex items-center justify-between gap-2') }}
                >
                    <span>{{ data_get($item, $itemLabel) }}</span>

                    @if ($select)
                        <tk:button
                            :attributes="$attributes->prefixed('select:')->merge(['label' => 'Use this'])"
                            :action="$select.'('.Js::from($key).')'"
                            variant="ghost"
                            size="sm"
                        />
                    @endif
                </li>
            @endforeach
        </ul>

        <tk:text
            :label="$message"
            variant="none"
            :$size
        />

        <div {{ $attributes->prefixed('actions:')->classes('flex items-center gap-2 mt-3') }}>
            <tk:button
                :attributes="$attributes->prefixed('confirm:')->merge(['label' => $updating ? 'Save anyway' : 'Create anyway'])"
                :action="$confirm"
                variant="warning"
                :$size
            />

            <tk:button
                :attributes="$attributes->prefixed('cancel:')->merge(['label' => 'Cancel'])"
                :action="$cancel"
                :$size
            />
        </div>
    </tk:alert>
@endif

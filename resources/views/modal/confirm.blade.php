@props([
    'size' => null,
    'confirm' => null,
    'cancel' => null,
    'variant' => null,
    'auto-close' => null,
    'title' => null,
    'subtitle' => null,
    'actions' => null,
])
@php

$autoClose = ${'auto-close'} ?? $attributes->pluck('autoClose');

@endphp
<tk:modal
    :attributes="$attributes->whereDoesntStartWith(['actions:', 'cancel:', 'confirm:'])->classes('max-w-sm')"
    :$size
    :title="$title ?? match ($variant) {
        default => 'Are you sure?',
        'delete' => 'Do you really want to delete this record?',
    }"
    :subtitle="$subtitle ?? match ($variant) {
        default => 'Do you really want to proceed with this action?',
        'delete' => 'This action is permanent and cannot be undone. All related data will be lost.',
    }"
>
    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    <div {{ TALLKit::attributesAfter($attributes, 'actions:')->classes('flex items-center gap-2 mt-10') }}>
        @isset ($actions)
            {{ $actions }}
        @else
            <tk:modal.close
                :attributes="TALLKit::attributesAfter($attributes, 'cancel:')"
                :$size
                :action="$cancel"
                label="Cancel"
            />

            <tk:button
                :attributes="TALLKit::attributesAfter($attributes, 'confirm:')
                    ->dataKey($autoClose === false ? null : 'modal-auto-close')
                    ->classes('ms-auto')
                "
                :$size
                :action="$confirm ?? $variant ?? 'confirm'"
                :variant="match ($variant) {
                    default => 'inverse',
                    'delete' => 'danger',
                }"
                label="Confirm"
            />
        @endisset
    </div>
</tk:modal>

@props([
    'size' => null,
    'confirm' => null,
    'cancel' => null,
    'variant' => null,
    'autoClose' => null,
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'description' => null,
    'prepend' => null,
    'append' => null,
])
<tk:modal
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['description' => null, 'prepend' => null, 'append' => null])->whereDoesntStartWith(['actions:', 'cancel:', 'confirm:'])
        ->classes('[:where(&)]:max-w-sm')
        ->merge(['role' => 'alertdialog'])"
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
    @if ($slot->isNotEmpty())
        <x-slot:trigger>
            {{ $slot }}
        </x-slot:trigger>
    @endif

    <div {{ $attributes->prefixed('actions:')->classes('flex items-center gap-2 mt-10') }}>
        @isset ($actions)
            {{ $actions }}
        @else
            {{-- Focus starts on the way out: an Enter pressed out of habit mustn't confirm. --}}
            <tk:modal.close
                :attributes="$attributes->prefixed('cancel:')->merge(['label' => 'Cancel'])"
                :$size
                :action="$cancel"
                autofocus
            />

            {{-- Only what it is told to call: no method guessed from the variant. --}}
            <tk:button
                :attributes="$attributes->prefixed('confirm:')
                    ->dataKey($autoClose === false ? null : 'modal-auto-close')
                    ->classes('ms-auto')
                    ->merge(['label' => 'Confirm'])
                "
                :$size
                :action="$confirm"
                x-on:click="$dispatch('confirmed')"
                :variant="match ($variant) {
                    default => 'inverse',
                    'delete' => 'danger',
                }"
            />
        @endisset
    </div>
</tk:modal>

@php
$iconClasses = TALLKit::classes();
$wireTarget = null;
@endphp
<tk:field
    :attributes="$attributes
        ->whereStartsWith(['label:', 'description:', 'help:', 'error:'])
        ->merge($attributesAfter('field:')->getAttributes())
    "
    :$name
    :$id
>
    <x-slot name="label" :attributes="$attributesFromSlot($label)">{{ $label }}</x-slot>
    <x-slot name="description" :attributes="$attributesFromSlot($description)">{{ $description }}</x-slot>
    <x-slot name="help" :attributes="$attributesFromSlot($help)">{{ $help }}</x-slot>

    <div {{ $attributes->only('class')->classes('w-full relative block group/input') }}>
        <?php if (is_string($iconLeading)): ?>
        <div class="pointer-events-none absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400/75 ps-3 start-0">
            <tk:icon :icon="$iconLeading" :class="$iconClasses" />
        </div>
    <?php elseif ($iconLeading): ?>
        <div {{ $iconLeading->attributes->class('absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400/75 ps-3 start-0') }}>
            {{ $iconLeading }}
        </div>
    <?php endif; ?>

        <input
            type="{{ $type }}"
            @isset ($name) name="{{ $name }}" @endisset
            @isset ($id) id="{{ $id }}" @endisset
            @if (in_livewire() && $name) wire:model="{{ $name }}" @endif
            {{
                $attributes
                    ->except('class')
                    ->whereDoesntStartWith(['field:', 'label:', 'description:', 'help:', 'error:'])
                    ->classes(match($size) {
                        'sm' => 'p-2 text-xs',
                        'normal' => 'p-3 text-sm',
                        default => 'p-3 text-sm',
                        'large' => 'p-4 text-base',
                    })
                    ->classes($rounded ? 'rounded-lg' : '')
                    ->classes('
                        peer
                        border border-gray-300 text-gray-900

                        bg-white
                        block
                        w-full

                        dark:bg-gray-700
                        dark:border-gray-600
                        dark:placeholder-gray-400
                        dark:text-white

                        focus:outline-hidden
                        focus:ring-2
                        focus:ring-blue-500
                        focus:ring-offset-2
                        focus:ring-offset-neutral-800
                    ')
                    ->when($invalid, fn($c) => $c->classes('border-red-500'))
                    ->classes($attributes->pluck('class:input'))
            }}
        />

        <div class="absolute top-0 bottom-0 flex items-center gap-x-1.5 pe-3 end-0 text-xs text-zinc-400">
            @if ($loading)
                <tk:loading :class="$iconClasses" wire:loading :wire:target="$wireTarget"/>
            @endif

            @if ($clearable)
                <tk:input.clearable inset="left right" :$size />
            @endif

            @if ($kbd)
                <span class="pointer-events-none">{{ $kbd }}</span>
            @endif

            @if ($expandable)
                <tk:input.expandable inset="left right" :$size />
            @endif

            @if ($copyable)
                <tk:input.copyable inset="left right" :$size />
            @endif

            @if ($viewable)
                <tk:input.viewable inset="left right" :$size />
            @endif

            <?php if (is_string($iconTrailing)): ?>
                <?php
                    $trailingIconClasses = clone $iconClasses;
                    $trailingIconClasses->add('pointer-events-none text-zinc-400/75');
                ?>
                <tk:icon :icon="$iconTrailing" :class="$trailingIconClasses" />
            <?php elseif ($iconTrailing): ?>
                {{ $iconTrailing }}
            <?php endif; ?>
        </div>
    </div>
</tk:field>

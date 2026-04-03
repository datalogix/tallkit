@props([
    'size' => null,
    'name' => null,
    'shortcut' => null,
    'dismissible' => null,
    'persist' => null,
    'closable' => null,
    'position' => null,
    'variant' => null,
    'backdrop' => null,
    'trigger' => null,

    // section
    'prepend' => null,
    'title' => null,
    'subtitle' => null,
    'description' => null,
    'append' => null,
    'actions' => null,
    'separator' => null,
    'content' => null,
])
@php

$name ??= Str::random();
$closable ??= $variant === 'bare' ? false : true;

@endphp
@isset ($trigger)
    <tk:modal.trigger
        :attributes="TALLKit::attributesAfter($attributes, 'trigger:')"
        :$name
        :$shortcut
    >
        {{ $trigger }}
    </tk:modal.trigger>
@endisset
<dialog
    wire:ignore.self
    x-data="modal(@js($name), @js($dismissible), @js($persist), @js($shortcut))"
    {{
        $attributes->whereDoesntStartWith([
            'trigger:', 'close:', 'section:',
            'container:', 'title:', 'icon', 'badge', 'subtitle:', 'list:', 'actions:', 'separator:', 'content:',
        ])->classes(
            '
                outline-none
                focus-visible:outline-none

                [&:open]:opacity-100
                [&:open]:scale-100
                [&:open]:duration-150
                [&:open]:translate-x-0
                [&:open]:transition-all
                [&:open]:transition-discrete

                [&:open]:backdrop:opacity-100
                [&:open]:backdrop:scale-100
                [&:open]:backdrop:duration-150
                [&:open]:backdrop:translate-x-0
                [&:open]:backdrop:transition-all
                [&:open]:backdrop:transition-discrete

                starting:opacity-0
                starting:backdrop:opacity-0
            ',
            match ($backdrop) { // Backdrop...
                'strong' => 'backdrop:bg-black/90',
                'subtle' => 'backdrop:bg-black/10',
                false, 'ghost' => 'backdrop:hidden',
                default => 'backdrop:bg-black/50',
            },
            match ($variant) { // Min-Max width...
                default => 'fixed m-auto overflow-auto [:where(&)]:max-w-xl [:where(&)]:min-w-xs',
                'flyout' => match ($position) {
                    'top' => 'fixed m-0 min-w-[100vw] border-b',
                    'bottom' => 'fixed m-0 min-w-[100vw] border-t',
                    'left' => 'fixed m-0 max-h-dvh min-h-dvh border-e',
                    default => 'fixed m-0 max-h-dvh min-h-dvh border-s',
                },
                'floating' => match ($position) {
                    'top' => 'fixed m-2 min-w-[calc(100vw-1rem)]',
                    'bottom' => 'fixed m-2 min-w-[calc(100vw-1rem)]',
                    default => 'fixed m-2 max-h-[calc(100dvh-1rem)] min-h-[calc(100dvh-1rem)]',
                },
                'bare' => '',
            },
            match ($variant) { // Positions...
                 default => 'starting:transform-[scale(0.95)]',
                'flyout', 'floating' => match ($position) {
                    'top' => '
                        overflow-y-auto
                        mb-auto
                        [&:open]:starting:-translate-y-[50px]
                    ',
                    'bottom' => '
                        overflow-y-auto
                        mt-auto
                        [&:open]:starting:translate-y-[50px]
                    ',
                    'left' => '
                        md:[:where(&)]:min-w-[25rem]
                        overflow-y-auto
                        mr-auto
                        [&:open]:starting:translate-x-[-50px]
                        rtl:mr-0
                        rtl:ml-auto
                        rtl:[&:open]:starting:translate-x-[50px]
                    ',
                    default => '
                        md:[:where(&)]:min-w-[25rem]
                        overflow-y-auto ml-auto
                        [&:open]:starting:translate-x-[50px]
                        rtl:ml-0
                        rtl:mr-auto
                        rtl:[&:open]:starting:translate-x-[-50px]
                    ',
                },
            },
            match ($variant) { // Border color, Rings...
                default => 'ring ring-black/5 dark:ring-zinc-700',
                'flyout' => 'border-transparent dark:border-zinc-700',
                'bare' => '',
            },
            match ($variant) { // Background color...
                default => '[:where(&)]:bg-white dark:[:where(&)]:bg-zinc-800',
                'bare' => '[:where(&)]:bg-transparent',
            },
            match ($variant) { // Shadows...
                default => 'shadow-lg rounded-xl',
                'flyout', 'bare' => '',
            },
        )
        ->merge(['data-modal' => $name])
    }}
>
    <span tabindex="0" class="sr-only"></span>

    <tk:section
        :attributes="TALLKit::attributesAfter($attributes, 'section:', prepend: [
                'icon', 'badge',
                'container:', 'title:', 'subtitle:', 'list:', 'actions:', 'separator:', 'content:',
            ])
            ->classes($variant === 'bare' ? '' : 'p-6', '[:where(&)]:text-start')
        "
        :$size
        :$prepend
        :$title
        :$subtitle
        :$description
        :$append
        :$content
        :separator="$separator ?? false"
    >
        {{ $slot }}

        <x-slot:actions>
            {{ $actions }}

            @if (isset($close))
                {{ $close }}
            @elseif ($closable !== false)
                <tk:modal.close
                    :attributes="TALLKit::attributesAfter($attributes, 'close:')->classes('absolute top-0 end-0 mt-4 me-4')"
                    :size="TALLKit::adjustSize(size: $size)"
                    variant="ghost"
                    icon="close"
                    aria-label="Close modal"
                />
            @endif
        </x-slot:actions>
    </tk:section>
</dialog>

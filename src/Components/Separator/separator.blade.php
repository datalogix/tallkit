@php
$classes = $classes('border-0 [print-color-adjust:exact]')
    ->add(match ($variant) {
        'strong' => 'bg-zinc-800 dark:bg-white',
        'subtle' => 'bg-zinc-800/5 dark:bg-white/10',
        default => 'bg-zinc-800/15 dark:bg-white/20',
    })
    ->add(match ($vertical) {
        true => 'self-stretch self-center w-px',
        default => 'h-px w-full',
    })
    ;
@endphp

@if ($slot->isNotEmpty() || $text)
    <div class="flex items-center w-full" role="none" data-tallkit-separator>
        <div {{ $attributes->whereDoesntStartWith(['content:'])->classes($classes->add('grow')) }}></div>

        <span {{ $attributesAfter('content:')->classes('shrink mx-6 font-medium text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap') }}>
            {{ $slot->isEmpty() ? __($text) : $slot }}
        </span>

        <div {{ $attributes->whereDoesntStartWith(['content:'])->classes($classes->add('grow')) }}></div>
    </div>
@else
    <div role="none" {{ $attributes->whereDoesntStartWith(['content:'])->classes($classes->add('shrink-0')) }} data-tallkit-separator></div>
@endif

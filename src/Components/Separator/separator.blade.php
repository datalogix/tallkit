@php
$contentClasses = $classes('border-0 [print-color-adjust:exact]')
    ->add(match ($variant) {
        'accent' => 'bg-[var(--color-accent)]',
        'strong' => 'bg-zinc-800/50 dark:bg-white/50',
        'subtle' => 'bg-zinc-800/10 dark:bg-white/10',
        default => 'bg-zinc-800/20 dark:bg-white/20',
        'red' => 'bg-red-600 dark:bg-red-500',
        'orange' => 'bg-orange-600 dark:bg-orange-500',
        'amber' => 'bg-amber-600 dark:bg-amber-500',
        'yellow' => 'bg-yellow-600 dark:bg-yellow-500',
        'lime' => 'bg-lime-600 dark:bg-lime-500',
        'green' => 'bg-green-600 dark:bg-green-500',
        'emerald' => 'bg-emerald-600 dark:bg-emerald-500',
        'teal' => 'bg-teal-600 dark:bg-teal-500',
        'cyan' => 'bg-cyan-600 dark:bg-cyan-500',
        'sky' => 'bg-sky-600 dark:bg-sky-500',
        'blue' => 'bg-blue-600 dark:bg-blue-500',
        'indigo' => 'bg-indigo-600 dark:bg-indigo-500',
        'violet' => 'bg-violet-600 dark:bg-violet-500',
        'purple' => 'bg-purple-600 dark:bg-purple-500',
        'fuchsia' => 'bg-fuchsia-600 dark:bg-fuchsia-500',
        'pink' => 'bg-pink-600 dark:bg-pink-500',
        'rose' => 'bg-rose-600 dark:bg-rose-500',
    })
    ->add(match ($vertical) {
        true => 'self-stretch self-center w-px h-full',
        default => 'h-px w-full',
    })
    ;
@endphp

@if ($slot->isNotEmpty() || $label)
    <div role="none" class="flex items-center w-full">
        <div {{ $attributes->whereDoesntStartWith(['content:'])->classes($contentClasses->add('grow')) }}></div>

        <span {{ $attributesAfter('content:')->classes('shrink mx-6 font-medium text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap') }}>
            {{ $slot->isEmpty() ? __($label) : $slot }}
        </span>

        <div {{ $attributes->whereDoesntStartWith(['content:'])->classes($contentClasses->add('grow')) }}></div>
    </div>
@else
    <div role="none" {{ $attributes->whereDoesntStartWith(['content:'])->classes($contentClasses->add('shrink-0')) }}></div>
@endif

@if ($slot->isNotEmpty() || $text)
    <div class="flex items-center w-full" role="none" >
        <div {{ $attributes->classes('border-0')
            ->classes(match ($variant) {
               'subtle' => 'bg-zinc-800/5 dark:bg-white/10',
               default => 'bg-zinc-800/15 dark:bg-white/20',
           })
           ->classes(match ($orientation) {
               'horizontal' => 'h-px w-full grow',
               'vertical' => 'self-stretch self-center w-px shrink-0',
           }) }}></div>

        <span class="shrink mx-6 font-medium text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
            {{ $slot->isEmpty() ? $text : $slot }}
        </span>

        <div {{ $attributes->classes('border-0')
            ->classes(match ($variant) {
               'subtle' => 'bg-zinc-800/5 dark:bg-white/10',
               default => 'bg-zinc-800/15 dark:bg-white/20',
           })
           ->classes(match ($orientation) {
               'horizontal' => 'h-px w-full grow',
               'vertical' => 'self-stretch self-center w-px shrink-0',
           }) }}></div>
    </div>
@else
    <div role="none" {{ $attributes->classes('border-0 shrink-0')
     ->classes(match ($variant) {
        'subtle' => 'bg-zinc-800/5 dark:bg-white/10',
        default => 'bg-zinc-800/15 dark:bg-white/20',
    })
    ->classes(match ($orientation) {
        'horizontal' => 'h-px w-full',
        'vertical' => 'self-stretch self-center w-px',
    }) }}></div>
@endif

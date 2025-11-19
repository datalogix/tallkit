<div {{ $attributes->classes(
    '[:where(&)]:w-full',
    match ($size) {
        default => '[:where(&)]:h-5 py-[3px]',
        'lg' => 'h-6 py-[2px]',
        'xl' => 'h-8 py-[3px]',
    }
) }}>
    <div class="h-full [:where(&)]:rounded [:where(&)]:bg-zinc-400/20">
        {{ $slot }}
    </div>
</div>

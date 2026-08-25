@props([
    'livewire' => null,
])
<tk:html :appearance="false">
    <div class="grid grid-cols-2 h-dvh">
        <div class="p-6 space-y-4 dark bg-zinc-900 text-white/70">
            <tk:heading as="h2" label="Dark" />
            <div>
                {{ $slot }}

                @if ($livewire)
                    @livewire($livewire)
                @endif
            </div>
        </div>
        <div class="p-6 space-y-4 bg-white text-zinc-700">
            <tk:heading as="h2" label="Light" />
            <div>
                {{ $slot }}

                @if ($livewire)
                    @livewire($livewire)
                @endif
            </div>
        </div>
    </div>
</tk:html>

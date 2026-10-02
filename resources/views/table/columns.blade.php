@aware(['verticalLines', 'sticky', 'dense'])
{{-- Built, not written: a literal opening tag in a directive's arguments unbalances Livewire's precompiler. --}}
@php($slotHas = fn (string $tag) => Str::contains($slot, '<'.$tag, true))
<thead {{ $attributes->prefixed('head:')->classes([
    '[:where(&)]:bg-zinc-100/80 dark:[:where(&)]:bg-zinc-800/80' => $dense,
    'sticky z-20 top-0 [:where(&)]:bg-white/95 dark:[:where(&)]:bg-zinc-800/95 shadow' => $sticky,
    '[&>tr]:divide-x [&>tr]:divide-zinc-800/10 [&>tr]:dark:divide-white/20' => $verticalLines,
    '[&>tr]:border-b [&>tr]:border-zinc-800/10 [&>tr]:dark:border-white/20',
]) }}>
    @if (! $slotHas('tr'))
        <tk:table.columns.row :attributes="$attributes->whereDoesntStartWith(['head:'])">
            {{ $slot }}
        </tk:table.columns.row>
    @else
        {{ $slot }}
    @endif
</thead>

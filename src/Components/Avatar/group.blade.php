<div {{ $attributes
    ->classes('flex isolate -space-x-2 rtl:space-x-reverse')
    ->classes('**:ring-2')
    ->classes('**:ring-white')
    ->classes('**:dark:ring-zinc-900')
}}>
    {{ $slot }}
</div>

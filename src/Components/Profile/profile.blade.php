<div class="flex items-center gap-2.5">
    <div {{ $attributes
        ->classes(match($size) {
            'xl' => '[:where(&)]:size-16 [:where(&)]:text-base',
            'lg' => '[:where(&)]:size-12 [:where(&)]:text-base',
            default => '[:where(&)]:size-10 [:where(&)]:text-sm',
            'sm' => '[:where(&)]:size-8 [:where(&)]:text-sm',
            'xs' => '[:where(&)]:size-6 [:where(&)]:text-xs',
        })
        ->when($circle,
            fn ($c) => $c->classes('after:rounded-full rounded-full'),
            fn ($c) => $c->classes(match($size) {
                'xl' => 'after:rounded-2xl rounded-2xl',
                'lg' => 'after:rounded-xl rounded-xl',
                default => 'after:rounded-lg rounded-lg',
                'sm' => 'after:rounded-md rounded-md',
                'xs' => 'after:rounded-sm rounded-sm',
            })
        )
        ->classes('relative isolate flex items-center justify-center')
        ->classes('[:where(&)]:font-medium [:where(&)]:text-gray-800 [:where(&)]:bg-gray-100')
        ->classes('[:where(&)]:dark:bg-gray-600 [:where(&)]:dark:text-white')
        ->classes('after:absolute after:inset-0 after:inset-ring-[1px] after:inset-ring-black/7 dark:after:inset-ring-white/10')
        ->classes(match($color) {
            'red' => 'bg-red-200 text-red-800',
            'orange' => 'bg-orange-200 text-orange-800',
            'amber' => 'bg-amber-200 text-amber-800',
            'yellow' => 'bg-yellow-200 text-yellow-800',
            'lime' => 'bg-lime-200 text-lime-800',
            'green' => 'bg-green-200 text-green-800',
            'emerald' => 'bg-emerald-200 text-emerald-800',
            'teal' => 'bg-teal-200 text-teal-800',
            'cyan' => 'bg-cyan-200 text-cyan-800',
            'sky' => 'bg-sky-200 text-sky-800',
            'blue' => 'bg-blue-200 text-blue-800',
            'indigo' => 'bg-indigo-200 text-indigo-800',
            'violet' => 'bg-violet-200 text-violet-800',
            'purple' => 'bg-purple-200 text-purple-800',
            'fuchsia' => 'bg-fuchsia-200 text-fuchsia-800',
            'pink' => 'bg-pink-200 text-pink-800',
            'rose' => 'bg-rose-200 text-rose-800',
            default => '',
        })
    }}>
        @if ($src)
            <img src="{{ $src }}" alt="{{ $alt ?? $name }}" class="{{ $circle ? 'rounded-full' : 'rounded-sm' }}">
        @elseif($initials && !$icon)
            <span class="select-none">{{ $initials }}</span>
        @else
            <tk:icon name="user" class="shrink-0 opacity-75" />
        @endif
    </div>

    @if ($name || $description || $slot->isNotEmpty())
        <div class="font-medium dark:text-white flex flex-col -space-y-1">
            @if ($name)
                <div>{{ $name }}</div>
            @endif

            @if ($slot->isNotEmpty() || $description )
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $slot->isEmpty() ? __($description) : $slot }}
                </div>
            @endif
        </div>
    @endif
</div>

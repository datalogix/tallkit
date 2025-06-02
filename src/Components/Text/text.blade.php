@if ($slot->isNotEmpty() || $text)
    <tk:element :$inline :attributes="$attributes
        ->classes(match ($size) {
            '3xl' => 'text-2xl',
            '2xl' => 'text-xl',
            'xl' => 'text-lg',
            'lg' => 'text-base',
            default => 'text-sm',
            'sm' => 'text-xs',
        })
        ->classes($color ? match($color) {
            'red' => 'text-red-600 dark:text-red-400',
            'orange' => 'text-orange-600 dark:text-orange-400',
            'amber' => 'text-amber-600 dark:text-amber-500',
            'yellow' => 'text-yellow-600 dark:text-yellow-500',
            'lime' => 'text-lime-600 dark:text-lime-500',
            'green' => 'text-green-600 dark:text-green-500',
            'emerald' => 'text-emerald-600 dark:text-emerald-400',
            'teal' => 'text-teal-600 dark:text-teal-400',
            'cyan' => 'text-cyan-600 dark:text-cyan-400',
            'sky' => 'text-sky-600 dark:text-sky-400',
            'blue' => 'text-blue-600 dark:text-blue-400',
            'indigo' => 'text-indigo-600 dark:text-indigo-400',
            'violet' => 'text-violet-600 dark:text-violet-400',
            'purple' => 'text-purple-600 dark:text-purple-400',
            'fuchsia' => 'text-fuchsia-600 dark:text-fuchsia-400',
            'pink' => 'text-pink-600 dark:text-pink-400',
            'rose' => 'text-rose-600 dark:text-rose-400',
            default => '',
        } : match ($variant) {
            'strong' => '[:where(&)]:text-zinc-800 [:where(&)]:dark:text-white',
            'subtle' => '[:where(&)]:text-zinc-400 [:where(&)]:dark:text-white/50',
            default => '[:where(&)]:text-zinc-500 [:where(&)]:dark:text-white/70',
        })
    " data-tallkit-text>
        {{ $slot->isEmpty() ? __($text) : $slot }}
    </tk:element>
@endif

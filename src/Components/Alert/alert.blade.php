<div
    x-data="alertComponent(@js($timeout))"
    role="alert"
    {{
        $attributes
            ->whereDoesntStartWith(['icon:', 'content:', 'title:', 'text:', 'list:', 'list-item:', 'actions:', 'dismissible-content:', 'dismissible:'])
            ->classes(
                'transition-opacity duration-300 opacity-100',
                'flex p-4 mb-4 text-base',
                match ($type) {
                    'danger' => 'text-red-800 bg-red-100 dark:bg-zinc-700 dark:text-red-300',
                    'success' => 'text-green-800 bg-green-100 dark:bg-zinc-700 dark:text-green-300',
                    'warning' => 'text-yellow-800 bg-yellow-100 dark:bg-zinc-700 dark:text-yellow-300',
                    'info' => 'text-blue-800 bg-blue-100 dark:bg-zinc-700 dark:text-blue-300',
                    default => 'text-zinc-800 bg-zinc-100 dark:bg-zinc-700 dark:text-zinc-300',
                },
                match ($border) {
                    'top' => 'border-t-3',
                    'left' => 'border-l-3',
                    'right' => 'border-r-3',
                    'bottom' => 'border-b-3',
                    true => 'border rounded-lg',
                    default => 'border-none rounded-lg',
                },
            )
            ->when(
                $border,
                fn ($c) => $c->classes(match ($type) {
                    'danger' => 'border-red-300 dark:border-red-200',
                    'success' => 'border-green-300 dark:border-green-200',
                    'warning' => 'border-yellow-300 dark:border-yellow-200',
                    'info' => 'border-blue-300 dark:border-blue-200',
                    default => 'border-zinc-300 dark:border-zinc-200',
                })
            )
    }}
>
    @if ($icon !== false)
        <tk:icon
            :attributes="$attributesAfter('icon:')->classes('shrink-0 me-3')"
            :$size
            :icon="is_string($icon) ? $icon : match ($type) {
                'danger' => 'close-circle-outline',
                'success' => 'check-circle-outline',
                'warning' => 'warning-outline',
                default => 'info-circle-outline',
            }"
        />
    @endif

    <div {{ $attributesAfter('content:')->classes('flex-1 flex flex-col justify-center space-y-1.5') }}>
        <tk:heading
            :attributes="$attributesAfter('title:')->classes('text-current')"
            :label="$title"
            :$size
        />

        @if ($slot->isNotEmpty() || ($message && is_string($message)))
            <tk:text
                :attributes="$attributesAfter('text:')->classes('text-current')"
                :label="$message"
                :$size
            >
                {{ $slot }}
            </tk:text>
        @endif

        @if (is_array($message) && filled($message))
            <ul {{ $attributesAfter('list:')->classes('list-disc list-inside') }}>
                @foreach ($message as $item)
                    <li>
                        <tk:text
                            :attributes="$attributesAfter('list-item:')->classes('text-current')"
                            :label="$item"
                            :$size
                            as="span"
                        />
                    </li>
                @endforeach
            </ul>
        @endif

        @isset ($actions)
            <div {{ $attributesAfter('actions:')->classes('flex items-center gap-2 mt-4') }}>
                {{ $actions }}
            </div>
        @endisset
    </div>

    @if ($dismissible)
        <div {{ $attributesAfter('dismissible-content:', slot: 'dismissible')->classes('ms-auto -mx-1.5 -my-1.5') }}>
            @if ($isSlot($dismissible))
                {{ $dismissible }}
            @else
                <tk:alert.close
                    :attributes="$attributesAfter('dismissible:')"
                    :icon="is_string($dismissible) ? $dismissible : null"
                    :$type
                    :$size
                 />
            @endif
        </div>
    @endif
</div>

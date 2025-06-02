<div
    x-data="alertComponent(@js($timeout))"
    role="alert"
    {{ $attributes
        ->whereDoesntStartWith(['icon:', 'content:', 'title:', 'list:', 'dismissible-content:', 'dismissible:'])
        ->classes(
            'transition-opacity duration-300 opacity-100',
            'flex p-4 mb-4 text-base',
            match($type) {
                'danger' => 'text-red-800 bg-red-100 dark:bg-zinc-700 dark:text-red-300',
                'success' => 'text-green-800 bg-green-100 dark:bg-zinc-700 dark:text-green-300',
                'warning' => 'text-yellow-800 bg-yellow-100 dark:bg-zinc-700 dark:text-yellow-300',
                'info' => 'text-blue-800 bg-blue-100 dark:bg-zinc-700 dark:text-blue-300',
                default => 'text-zinc-800 bg-zinc-100 dark:bg-zinc-700 dark:text-zinc-300',
            },
            match($border) {
                'accent', 'top' => 'border-t-4',
                'left' => 'border-l-4',
                'right' => 'border-r-4',
                'bottom' => 'border-b-4',
                true => 'border rounded-lg',
                default => 'border-none rounded-lg',
            },
        )
        ->when($border, fn ($c) => $c->classes(match($type) {
            'danger' => 'border-red-300 dark:border-red-200',
            'success' => 'border-green-300 dark:border-green-200',
            'warning' => 'border-yellow-300 dark:border-yellow-200',
            'info' => 'border-blue-300 dark:border-blue-200',
            default => 'border-gray-300 dark:border-gray-200',
        }))
}} data-tallkit-alert>
    @if ($icon !== false)
        <tk:icon
            :attributes="$attributesAfter('icon:')->classes('shrink-0 inline me-3 w-6 h-6')"
            :icon="is_string($icon) ? $icon : match($type) {
                'danger' => 'close-circle-outline',
                'success' => 'check-circle-outline',
                'warning' => 'warning-outline',
                default => 'info-circle-outline',
            }"
            data-tallkit-alert-icon
        />
    @endif

    <div {{ $attributesAfter('content:')->classes('flex-1 space-y-1.5') }} data-tallkit-alert-content>
        @if ($title)
            <tk:heading :attributes="$attributesAfter('title:')->classes('text-current!')" data-tallkit-alert-title>
                {!! $isSlot($title) ? $title : __($title) !!}
            </tk:heading>
        @endif

        @if ($slot->isNotEmpty() || $message)
            <p>{{ $slot->isEmpty() ? __($message) : $slot }}</p>
        @endif

        @if ($list)
            <ul {{ $attributesAfter('list:')->classes('list-disc list-inside') }} data-tallkit-alert-list>
                @foreach ($list as $message)
                    <li>{!! __($message) !!}</li>
                @endforeach
            </ul>
        @endif
    </div>

    @if ($dismissible)
        <div {{ $attributesAfter('dismissible-content:', slot: 'dismissible')->classes('ms-auto -mx-1.5 -my-1.5') }} data-tallkit-alert-dismissible>
            @if ($isSlot($dismissible))
                {{ $dismissible }}
            @else
                <tk:alert.close
                    :attributes="$attributesAfter('dismissible:')"
                    :icon="is_string($dismissible) ? $dismissible : null"
                    :$type
                 />
            @endif
        </div>
    @endif
</div>

<?php

namespace TALLKit\Components\Alert;

use TALLKit\View\BladeComponent;

class Alert extends BladeComponent
{
    public function __construct(
        public ?string $type = null,
        public string|bool $icon = true,
        public string|bool $border = false,
        public ?string $title = null,
        public ?string $message = null,
        public ?array $list = null,
        public bool $dismissible = false,
        public ?string $dismissibleIcon = null,
        public int|bool|null $timeout = null,
    ) {}

    public function render()
    {
        return <<<'BLADE'
        <div
            x-data="alertComponent(@js($timeout))"
            role="alert"
            {{ $attributes
                ->whereDoesntStartWith(['icon:', 'container:', 'title:', 'list:', 'dismissible'])
                ->classes('transition-opacity duration-300 opacity-100')
                ->classes('flex p-4 mb-4 text-sm')
                ->classes(match($type) {
                    'danger' => 'text-red-800 bg-red-50 dark:bg-gray-800 dark:text-red-400',
                    'success' => 'text-green-800 bg-green-50 dark:bg-gray-800 dark:text-green-400',
                    'warning' => 'text-yellow-800 bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300',
                    'info' => 'text-blue-800 bg-blue-50 dark:bg-gray-800 dark:text-blue-400',
                    default => 'text-gray-800 bg-gray-50 dark:bg-gray-800 dark:text-gray-300',
                })
                ->classes(match($border) {
                    'accent', 'top' => 'border-t-4',
                    'left' => 'border-l-4',
                    'right' => 'border-r-4',
                    'bottom' => 'border-b-4',
                    true => 'border rounded-lg',
                    default => 'border-none rounded-lg',
                })
                ->when($border, fn ($c) => $c->classes(match($type) {
                    'danger' => 'border-red-300 dark:border-red-800',
                    'success' => 'border-green-300 dark:border-green-800',
                    'warning' => 'border-yellow-300 dark:border-yellow-800',
                    'info' => 'border-blue-300 dark:border-blue-800',
                    default => 'border-gray-300 dark:border-gray-600',
                }))
        }}>
            @if ($icon)
                <tk:icon
                    {{ $attributesAfter('icon:')
                        ->classes('shrink-0 inline me-3 mt-[2px]')
                        ->classes($title ? 'w-5 h-5' : 'w-4 h-4')
                     }}
                    icon="{{ $icon === true ? match($type) {
                        'danger' => 'ph:x-circle',
                        'success' => 'ph:check-circle',
                        'warning' => 'ph:warning',
                        default =>'ph:info',
                    } : $icon }}"
                />
            @endif

            <div {{ $attributesAfter('container:')->classes('flex-1 space-y-1.5') }}>
                @if ($title)
                    <tk:heading {{ $attributesAfter('title:') }}>{{ $title }}</tk:heading>
                @endif

                @if ($message || $slot->isNotEmpty())
                    <p>{{ $message ?? $slot }}</p>
                @endif

                @if ($list)
                    <ul {{ $attributesAfter('list:')->classes('list-disc list-inside') }}>
                        @foreach ($list as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if ($dismissible === true || $dismissible instanceOf \Illuminate\View\ComponentSlot)
                <div {{ $attributesAfter('dismissible:container')->classes('ms-auto -mx-1.5 -my-1.5') }}>
                    @if ($dismissible instanceOf \Illuminate\View\ComponentSlot && $dismissible->isNotEmpty())
                        {{ $dismissible }}
                    @else
                        <button
                            @click="dismiss"
                            type="button"
                            {{ $attributesAfter('dismissible-button:')->classes('
                                rounded-lg
                                focus:ring-2
                                p-1.5
                                inline-flex items-center justify-center
                                h-8 w-8
                                dark:bg-gray-800
                                dark:hover:bg-gray-700
                            ')->classes(match($type) {
                                'danger' => 'bg-red-50 text-red-500 focus:ring-red-400 hover:bg-red-200 dark:text-red-400',
                                'success' => 'bg-green-50 text-green-500 focus:ring-green-400 hover:bg-green-200 dark:text-green-400',
                                'warning' => 'bg-yellow-50 text-yellow-500 focus:ring-yellow-400 hover:bg-yellow-200 dark:text-yellow-300',
                                'info' => 'bg-blue-50 text-blue-500 focus:ring-blue-400 hover:bg-blue-200 dark:text-blue-400',
                                default => 'bg-gray-50 text-gray-500 focus:ring-gray-400 hover:bg-gray-200 dark:text-gray-300 dark:hover:text-white',
                            }) }}
                            aria-label="{{ __('Close') }}"
                        >
                            <span class="sr-only">{{ __('Close') }}</span>
                            <tk:icon
                                {{ $attributesAfter('dismissible-icon:') }}
                                icon="{{ $dismissibleIcon ?? 'ph:x' }}"
                            />
                        </button>
                    @endif
                </div>
            @endif
        </div>
        BLADE;
    }
}

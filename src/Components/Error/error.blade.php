@php
$errorBag = $errors->getBag($bag ?? 'default');
$message ??= $name ? $errorBag->first($name) : $slot;
@endphp

<div {{
        $attributes->whereDoesntStartWith(['icon:'])
            ->classes([
                'font-medium text-red-400 flex items-center gap-2',
                 match ($size) {
                    'xs' => 'text-[11px]',
                    'sm' => 'text-xs',
                    default => 'text-sm',
                    'lg' => 'text-base',
                    'xl' => 'text-lg',
                    '2xl' => 'text-xl',
                    '3xl' => 'text-2xl',
                },
                'hidden' => blank($message)
            ])
    }}
    role="alert"
    aria-live="polite"
    aria-atomic="true"
>
    @if ($message && $icon !== false)
        <tk:icon
            :attributes="$attributesAfter('icon:')"
            :name="is_string($icon) ? $icon : 'alert-outline'"
            :size="$adjustSize(move: -2)"
        />
    @endif

    {{ $message }}
</div>

@props([
    'items' => null,
    'size' => null,
    'empty' => null,
])
@php

$notifications = collect($items ?? auth()->user()?->unreadNotifications ?? []);

@endphp
<div
    {{
        $attributes
            ->whereDoesntStartWith(['item:', 'empty:'])
            ->classes('m-1')
    }}
>
    @forelse ($notifications as $notification)
        <tk:notification.item
            :attributes="TALLKit::attributesAfter($attributes, 'item:')"
            :$notification
            :$size
        />
    @empty
        <tk:text
            :attributes="TALLKit::attributesAfter($attributes, 'empty:')
                ->classes(
                    'block text-center',
                    TALLKit::padding(size: $size),
                )
            "
            :$size
            variant="subtle"
            label="{{ $empty ?? 'No new notifications' }}"
        />
    @endforelse
</div>

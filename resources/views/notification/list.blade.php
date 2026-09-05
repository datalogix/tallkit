@props([
    'items' => null,
    'size' => null,
    'empty' => null,
    'grouped' => null,
    'compact' => null,
])
@php

$notifications = collect($items ?? auth()->user()?->unreadNotifications ?? []);

$dateLabel = function ($notification) {
    $data = data_get($notification, 'data', []);
    $date = Carbon\Carbon::parse(data_get($data, 'created_at') ?? data_get($notification, 'created_at'));

    return match (true) {
        $date->isToday() => 'Today',
        $date->isYesterday() => 'Yesterday',
        $date->greaterThanOrEqualTo(now()->subDays(7)) => 'This week',
        default => 'Earlier',
    };
};

$order = ['Today', 'Yesterday', 'This week', 'Earlier'];

$groups = $grouped
    ? $notifications->groupBy($dateLabel)->sortBy(fn ($group, $label) => array_search($label, $order))
    : ($notifications->isNotEmpty() ? collect([null => $notifications]) : collect());

@endphp
<div
    {{
        $attributes
            ->whereDoesntStartWith(['item:', 'empty:', 'group-label:'])
            ->classes('m-1')
    }}
>
    @forelse ($groups as $label => $group)
        @if ($label)
            <tk:text
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'group-label:')
                    ->classes('block px-2 pt-3 pb-1 first:pt-1 font-medium uppercase tracking-wide')
                "
                :size="TALLKit::adjustSize(size: $size)"
                variant="subtle"
                :$label
            />
        @endif

        @foreach ($group as $notification)
            <tk:notification.item
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'item:')"
                :$notification
                :$size
                :$compact
            />
        @endforeach
    @empty
        <tk:text
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'empty:')
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

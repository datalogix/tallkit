@props([
    'items' => [],
])
<tk:section
    :attributes="$attributes->whereDoesntStartWith(['table:', 'empty:'])->merge(['title' => 'Account activity', 'subtitle' => 'What happened to your account. If you don\'t recognize something, change your password.', 'separator' => false])"
>
    @if (count($items) === 0)
        <tk:text :attributes="$attributes->prefixed('empty:')->merge(['label' => 'Nothing happened to your account yet.'])" />
    @else
        <tk:table
            :attributes="$attributes->prefixed('table:')"
            border
            :cols="['event' => 'Event', 'device' => 'Device', 'ip_address' => 'IP address', 'when' => 'When']"
        >
            <tk:table.rows>
                @foreach ($items as $item)
                    <tk:table.row>
                        <tk:table.cell @class(['text-red-600 dark:text-red-400' => $item['failure'] ?? false])>{{ $item['description'] }}</tk:table.cell>
                        <tk:table.cell>{{ $item['device'] ?? __('Unknown device') }}</tk:table.cell>
                        <tk:table.cell>{{ $item['ip_address'] ?? __('Unknown IP') }}</tk:table.cell>
                        <tk:table.cell><tk:time :value="$item['created_at'] ?? null" /></tk:table.cell>
                    </tk:table.row>
                @endforeach
            </tk:table.rows>
        </tk:table>
    @endif
</tk:section>

@props([
    'items' => [],
    'revoke' => null,
    'revokeAll' => null,
])
@if (count($items) > 0)
    <div {{ $attributes->whereDoesntStartWith(['table:', 'revoke:', 'revoke-all:'])->classes('space-y-4') }}>
        <tk:table
            :attributes="$attributes->prefixed('table:')"
            border
            :cols="['device' => 'Device', 'ip_address' => 'IP address', 'last_active' => 'Last active', '']"
        >
            <tk:table.rows>
                @foreach ($items as $item)
                    <tk:table.row>
                        <tk:table.cell>{{ $item['device'] ?? $item['name'] ?? __('Unknown device') }}</tk:table.cell>
                        <tk:table.cell>{{ $item['ip_address'] ?? __('Unknown IP') }}</tk:table.cell>
                        <tk:table.cell>
                            @if ($item['current'] ?? false)
                                {{ __('This device') }}
                            @else
                                <tk:time :value="$item['last_active_at'] ?? $item['last_used_at'] ?? null" fallback="Never used" />
                            @endif
                        </tk:table.cell>
                        <tk:table.cell align="right">
                            @if ($revoke && ! ($item['current'] ?? false))
                                <tk:button
                                    :attributes="$attributes->prefixed('revoke:')"
                                    label="Sign out"
                                    :action="$revoke.'('.$item['id'].')'"
                                />
                            @endif
                        </tk:table.cell>
                    </tk:table.row>
                @endforeach
            </tk:table.rows>
        </tk:table>

        @if ($revokeAll && count($items) > 1)
            <tk:button
                :attributes="$attributes->prefixed('revoke-all:')"
                label="Sign out all the others"
                :action="$revokeAll"
            />
        @endif
    </div>
@endif

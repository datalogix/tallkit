@props([
    'codes' => [],
])
@if (count($codes) > 0)
    <div {{ $attributes->whereDoesntStartWith(['label:', 'list:', 'copy:'])->classes('space-y-2') }}>
        <tk:text :attributes="$attributes->prefixed('label:')->merge(['label' => 'Recovery codes (store them safely):'])" />

        <tk:list
            :attributes="$attributes->prefixed('list:')->classes('font-mono')"
            :items="$codes"
            marker="none"
        />

        <tk:copyable
            :attributes="$attributes->prefixed('copy:')->merge(['label' => 'Copy codes'])"
            :content="implode(PHP_EOL, $codes)"
        />
    </div>
@endif

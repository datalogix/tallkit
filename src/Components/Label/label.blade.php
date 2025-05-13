@if ($slot->isNotEmpty())
    <label {{ $attributes->classes('block mb-2 text-base font-medium text-gray-900 dark:text-white') }}>
        {{ $slot }}
    </label>
@endif

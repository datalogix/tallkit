<td {{ $attributes->classes('
    py-4 px-6
    text-sm text-zinc-500 dark:text-zinc-300
') }}" scope="row">
    {{ $slot->isEmpty() ? $value : $slot }}
</td>

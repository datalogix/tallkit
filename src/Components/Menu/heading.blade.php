
<div {{
    $attributes
        ->whereDoesntStartWith('container:')
        ->classes('p-2 pb-1 w-full flex items-center text-start')
}}>
    <div class="w-8.5 hidden [[data-tallkit-menu]:has(>[data-tallkit-menu-item-has-icon])_&]:block"></div>

    <tk:heading
        :attributes="$attributesAfter('container:')->classes('text-zinc-500 dark:text-zinc-300')"
        :size="$adjustSize(move: -2)"
        :$label
    >
        {{ $slot }}
    </tk:heading>
</div>

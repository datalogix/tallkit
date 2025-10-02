<tk:button.group
    :attributes="$attributes->whereDoesntStartWith(['system:', 'light:', 'dark:'])"
    x-data
>
    <tk:button
        :attributes="$attributesAfter('system:')"
        x-on:click="$tk.appearance.apply('system')"
        icon="ph:monitor"
        ::class="{ 'bg-current/20!': $tk.appearance.mode === 'system' }"
    />
    <tk:button
        :attributes="$attributesAfter('light:')"
        x-on:click="$tk.appearance.apply('light')"
        icon="ph:sun"
        ::class="{ 'bg-current/20!': $tk.appearance.mode === 'light' }"
    />
    <tk:button
        :attributes="$attributesAfter('dark:')"
        x-on:click="$tk.appearance.apply('dark')"
        icon="ph:moon"
        ::class="{ 'bg-current/20!': $tk.appearance.mode === 'dark' }"
    />
</tk:button.group>

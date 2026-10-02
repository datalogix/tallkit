<tk:button.group
    :attributes="$attributes->whereDoesntStartWith(['system:', 'light:', 'dark:'])"
    role="radiogroup"
    :aria-label="__('Theme')"
    x-data="appearanceSelector"
>
    <tk:button
        :attributes="$attributes->prefixed('system:')->merge(['tooltip' => 'System'])->classes('aria-checked:bg-current/10! dark:aria-checked:bg-current/30!')"
        icon="ph:monitor"
        role="radio"
        data-mode="system"
    />
    <tk:button
        :attributes="$attributes->prefixed('light:')->merge(['tooltip' => 'Light'])->classes('aria-checked:bg-current/10! dark:aria-checked:bg-current/30!')"
        icon="ph:sun"
        role="radio"
        data-mode="light"
    />
    <tk:button
        :attributes="$attributes->prefixed('dark:')->merge(['tooltip' => 'Dark'])->classes('aria-checked:bg-current/10! dark:aria-checked:bg-current/30!')"
        icon="ph:moon"
        role="radio"
        data-mode="dark"
    />
</tk:button.group>

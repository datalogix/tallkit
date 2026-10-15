@props([
    'items' => [],
    'signOut' => 'signOut',
])
<tk:section
    :attributes="$attributes->whereDoesntStartWith(['devices:'])->merge(['title' => 'Active sessions', 'subtitle' => 'Where your account is signed in. Sign out the sessions you don\'t recognize.', 'separator' => false])"
>
    <tk:page.auth.devices
        :attributes="$attributes->prefixed('devices:')->merge([
            'revoke-all:label' => 'Sign out other sessions',
            'revoke:wire:confirm' => __('Sign out this session?'),
            'revoke-all:wire:confirm' => __('Sign out all other sessions?'),
        ])"
        :$items
        :revoke="$signOut"
        :revoke-all="$signOut"
    />
</tk:section>

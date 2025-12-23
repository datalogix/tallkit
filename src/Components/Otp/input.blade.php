@aware(['mode', 'private'])
@props(['mode', 'private'])

<tk:input
    :attributes="$attributes->classes('w-8! grow-0 has-focus-within:z-10')"
    input:class="px-0! py-3 text-center disabled:opacity-75 disabled:shadow-xs! dark:disabled:shadow-none! uppercase"
    :type="$private ? 'password' : 'text'"
    :icon="false"
    :icon-trailing="false"
    :mask="false"
    :loading="false"
    :clearable="false"
    :kbd="false"
    :copyable="false"
    :viewable="false"
    data-mode="{{ $mode }}"
    inputmode="{{ $mode === null || $mode === 'numeric' ? 'numeric' : 'text' }}"
/>

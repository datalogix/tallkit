@aware(['mode', 'private', 'size'])
@props(['mode', 'private', 'size'])

<tk:input
    :attributes="$attributes->classes($width(size: $size))"
    :$size
    input:class="px-0! text-center uppercase"
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

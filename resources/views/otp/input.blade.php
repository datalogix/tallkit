@aware(['mode', 'private', 'size'])
@props(['mode', 'private', 'size'])
<tk:input
    :attributes="$attributes->except('input:class')"
    :$size
    :input:class="TALLKit::classes(
        $attributes->get('input:class'),
        'px-0! text-center uppercase',
        TALLKit::width(size: $size).'!',
    )"
    :type="$private ? 'password' : 'text'"
    :icon="false"
    :iconTrailing="false"
    :mask="false"
    :loading="false"
    :clearable="false"
    :kbd="false"
    :copyable="false"
    :viewable="false"
    maxlength="1"
    data-mode="{{ $mode ?? 'alphanumeric' }}"
    inputmode="{{ $mode === null || $mode === 'numeric' ? 'numeric' : 'text' }}"
/>

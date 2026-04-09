@aware(['mode', 'private', 'size'])
@props(['mode', 'private', 'size'])
<tk:input
    :attributes="$attributes->except('input:class')"
    :$size
    :input:class="TALLKit::classes(
        $attributes->get('input:class'),
        'px-0! text-center uppercase',
        match($size) {
            'xs' => 'w-8',
            'sm' => 'w-9',
            default => 'w-10',
            'lg' => 'w-12',
            'xl' => 'w-14',
            '2xl' => 'w-16',
            '3xl' => 'w-18',
        },
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
    data-mode="{{ $mode }}"
    inputmode="{{ $mode === null || $mode === 'numeric' ? 'numeric' : 'text' }}"
/>

@aware(['charset', 'private', 'size', 'color'])
@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'charset' => null,
    'private' => null,
    'size' => null,
    'color' => null,
    'first' => false,
    'total' => 1,
])
<tk:input
    :attributes="TALLKit::fieldWithProps($attributes, get_defined_vars())->except('input:class')"
    :$size
    :$color
    :input:class="TALLKit::classes(
        $attributes->get('input:class'),
        'px-0! text-center uppercase',
        TALLKit::width(size: $size).'!',
    )"
    :type="$private ? 'password' : 'text'"
    :icon="false"
    :iconTrailing="false"
    :show-error="false"
    :mask="false"
    :loading="false"
    :clearable="false"
    :kbd="false"
    :copyable="false"
    :viewable="false"
    :maxlength="$first ? $total : 1"
    :autocomplete="$first ? 'one-time-code' : 'off'"
    data-charset="{{ $charset ?? 'alphanumeric' }}"
    inputmode="{{ $charset === null || $charset === 'numeric' ? 'numeric' : 'text' }}"
/>

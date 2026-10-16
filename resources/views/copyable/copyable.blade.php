@props([
    ...TALLKit::elementProps(),
    'target' => null,
    'content' => null,
])
<tk:button
    wire:ignore.self
    x-cloak
    x-data="copyable({{ Js::from([
        'targetId' => $target,
        'content' => $content,
        'failedText' => __('Copy failed'),
    ]) }})"
    :attributes="$attributes->withElementProps(get_defined_vars())
        ->classes('min-w-6 min-h-6')
    "
    :x-bind:aria-label="has_content($ariaLabel) || has_content($label) || $slot->isNotEmpty()
        ? null
        : 'copied ? '.Js::from(__('Copied')).' : '.Js::from(__('Copy'))"
    variant="none"
    tooltip="Copied"
    :aria-label="has_content($ariaLabel) || has_content($label) || $slot->isNotEmpty() ? $ariaLabel : 'Copy'"
    tooltip:open="manual"
    icon="clipboard-multiple"
    icon:class="hidden"
    icon::class="{ 'hidden': copied }"
    iconTrailing="clipboard-check-multiple"
    icon-trailing:class="hidden"
    icon-trailing::class="{ 'hidden': !copied }"
>
    {{ $slot }}
</tk:button>

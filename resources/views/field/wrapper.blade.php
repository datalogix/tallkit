@props([
    ...TALLKit::fieldProps(),
    'inline' => null,
    'align' => null,
    'name' => null,
])
@php

$hasPrefix = ! $inline && ($prefix || $attributes->prefixed('prefix:')->isNotEmpty());
$hasSuffix = ! $inline && ($suffix || $attributes->prefixed('suffix:')->isNotEmpty());

// Same check as element.wrapper's (keep the lists alike): empty, each would still cost two components.
$shows = fn ($text, string $prefix) => $text || $attributes->prefixed($prefix)->only(['label', 'icon', 'prefix', 'suffix', 'icon-trailing', 'info', 'badge', 'prepend', 'append', 'kbd'])->filter(fn ($value) => (bool) $value)->isNotEmpty();
$hasDescription = $shows($description, 'description:');
$hasHelp = $shows($help, 'help:');

@endphp
@if ($label || $description || $help || $hasPrefix || $hasSuffix)
    <tk:field :$inline :$align :attributes="$attributes->prefixed('field:')">
        <tk:label
            :attributes="TALLKit::attributesMerge(
                $attributes->prefixed('label:'),
                $attributes->prefixed('info:', keepPrefix: true),
                $attributes->prefixed('badge:', keepPrefix: true),
            )"
            :for="$id"
            :$label
            :$labelPrepend
            :$labelAppend
            :$size
            :$info
            :$badge
        />

        @if ($hasDescription)
            <tk:text
                :attributes="$attributes->prefixed('description:')->merge(['id' => $id ? $id.'-description' : null])"
                :label="$description"
                :$size
            />
        @endif

        @if ($hasPrefix || $hasSuffix)
            <tk:field.group
                :attributes="TALLKit::attributesMerge(
                    $attributes->prefixed('group:'),
                    $attributes->prefixed('prefix:', keepPrefix: true),
                    $attributes->prefixed('suffix:', keepPrefix: true),
                )"
                :$prefix
                :$suffix
                :$size
            >
                {{ $slot }}
            </tk:field.group>
        @else
            {{ $slot }}
        @endif

        @if ($hasHelp)
            <tk:text
                :attributes="$attributes->prefixed('help:')->merge(['id' => $id ? $id.'-help' : null])"
                :label="$help"
                :$size
            />
        @endif

        @if ($showError !== false)
            <tk:error
                :attributes="$attributes->prefixed('error:')->merge(['id' => $id ? $id.'-error' : null])"
                :bag="TALLKit::fieldErrorBag($id)"
                :$name
                :$size
            />
        @endif
    </tk:field>
@else
    {{ $slot }}

    @if ($showError !== false && filled($name))
        <tk:error
            :attributes="$attributes->prefixed('error:')->merge(['id' => $id ? $id.'-error' : null])"
            :bag="TALLKit::fieldErrorBag($id)"
            :$name
            :$size
        />
    @endif
@endif

@php $invalid ??= $name && $errors->has($name); @endphp

<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
    label:as="span"
>
    <div
        wire:ignore
        {{ $attributesAfter('container:') }}
        {{ $buildDataAttribute('control') }}
        {{ $buildDataAttribute('group-target') }}
    >
        <trix-editor
            x-data
            x-on:trix-change="$dispatch('input', event.target.value)"
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @if ($placeholder) placeholder="{{ __($placeholder) }}" @endif
            {{
                $attributes->whereDoesntStartWith([
                    'field:', 'label:', 'information:', 'badge:', 'description:', 'help:', 'error:',
                    'group:', 'prefix:', 'suffix:',
                    'container:',
                ])
            }}
        ></trix-editor>
    </div>
</tk:field.wrapper>

@assets
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trix@2/dist/trix.css">
    <script src="https://cdn.jsdelivr.net/npm/trix@2/dist/trix.umd.min.js"></script>
@endassets

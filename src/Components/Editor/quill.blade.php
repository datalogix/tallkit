@php $invalid ??= $name && $errors->has($name); @endphp

<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
    label:as="span"
    label:container:@click="quill.focus()"
>
    <div
        wire:ignore
        {{ $attributesAfter('container:') }}
        {{ $buildDataAttribute('control') }}
        {{ $buildDataAttribute('group-target') }}
    >
        <div x-data="quill({
            @if ($placeholder) placeholder: '{{ __($placeholder) }}', @endif
            ...{{ $jsonOptions() }},
        })"></div>

        <input
            type="hidden"
            value="{{ $value }}"
            @isset ($name) name="{{ $name }}" @endisset
            @isset ($id) id="{{ $id }}" @endisset
            @if ($invalid) aria-invalid="true" data-invalid @endif
            {{
                $attributes->whereDoesntStartWith([
                    'field:', 'label:', 'information:', 'badge:', 'description:', 'help:', 'error:',
                    'group:', 'prefix:', 'suffix:',
                    'container:',
                ])
            }}
        />
    </div>
</tk:field>

@assets
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css">
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
@endassets

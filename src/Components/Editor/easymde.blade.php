@php $invalid ??= $name && $errors->has($name); @endphp

<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
    label:as="span"
    label:container:@click="easyMDE.codemirror.focus()"
>
    <div
        wire:ignore
        x-data="easymde({{ $jsonOptions() }})"
        {{ $attributesAfter('container:') }}
        {{ $buildDataAttribute('control') }}
        {{ $buildDataAttribute('group-target') }}
    >
        <textarea
            @isset ($name) name="{{ $name }}" @endisset
            @isset ($id) id="{{ $id }}" @endisset
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @if ($placeholder) placeholder="{{ __($placeholder) }}" @endif
            {{
                $attributes->whereDoesntStartWith([
                    'field:', 'label:', 'information:', 'badge:', 'description:', 'help:', 'error:',
                    'group:', 'prefix:', 'suffix:',
                    'container:',
                ])
            }}
        >{!! $value !!}</textarea>
    </div>
</tk:field>

@assets
    <link href="https://cdn.jsdelivr.net/npm/easymde@2/dist/easymde.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/easymde@2/dist/easymde.min.js"></script>
@endassets

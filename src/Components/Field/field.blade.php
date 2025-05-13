<div {{ $attributes->whereDoesntStartWith(['label:', 'description:', 'help:', 'error:']) }}>
    @if (filled($label))
        <tk:label for="{{ $id }}" :attributes="$attributesAfter('label:')">
            {{ $label }}
        </tk:label>
    @endif

    @if (filled($description))
        <tk:text :attributes="$attributesAfter('description:')">
            {{ $description }}
        </tk:text>
    @endif

    {{ $slot }}

    @if (filled($help))
        <tk:text :attributes="$attributesAfter('help:')">
            {{ $help }}
        </tk:text>
    @endif

    @error($name)
        <tk:field-error :attributes="$attributesAfter('error:')">
            {{ $message }}
        </tk:field-error>
    @enderror
</div>

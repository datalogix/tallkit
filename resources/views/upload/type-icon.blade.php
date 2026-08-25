@props([
    'size' => null,
    'variable' => 'file',
])
@foreach (['image', 'video', 'audio', 'pdf', 'doc', 'xls', 'ppt', 'archive', 'text', 'csv', 'code', 'unknown'] as $type)
    <template x-if="{{ $variable }}.type === '{{ $type }}'">
        <tk:icon
            :attributes="TALLKit::attributesAfter($attributes, 'file-' . $type . '-icon:')"
            :size="TALLKit::adjustSize($size)"
            name="ph:file-{{ $type }}"
        />
    </template>
@endforeach

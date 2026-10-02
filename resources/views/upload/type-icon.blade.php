@props([
    'size' => null,
    'variable' => 'file',
])
@php

$knownTypes = ['image', 'video', 'audio', 'pdf', 'doc', 'xls', 'ppt', 'archive', 'text', 'csv', 'code'];
$unknown = "!['".implode("', '", $knownTypes)."'].includes({$variable}.type)";

@endphp
@foreach ([...$knownTypes, 'unknown'] as $type)
    <template x-if="{{ $type === 'unknown' ? $unknown : "{$variable}.type === '{$type}'" }}">
        <tk:icon
            :attributes="$attributes->prefixed('file-' . $type . '-icon:')"
            :size="TALLKit::adjustSize(size: $size)"
            name="{{ $type === 'unknown' ? 'ph:file' : 'ph:file-'.$type }}"
        />
    </template>
@endforeach

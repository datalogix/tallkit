@props(['items' => [], 'row'])
@foreach ($items as $index => $item){{ $row($item, $index) }}@endforeach

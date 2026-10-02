@aware(['resizable'])
@if ($resizable)
    <colgroup {{ $attributes }}>
        {{ $slot }}
    </colgroup>
@endif

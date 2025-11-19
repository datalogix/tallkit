
<div {{ $attributesAfter('container:')->classes('-mx-[.4rem] my-[.4rem] h-px') }}>
    <tk:separator
        :attributes="$attributes->whereDoesntStartWith(['container:'])"
        :$variant
        :$label
     />
</div>

@props([
    'size' => null,
])
<tk:listbox
    :attributes="$attributes->classes(
        '
            [:where(&)]:bg-white [:where(&)]:dark:bg-zinc-700
            [:where(&)]:border [:where(&)]:border-zinc-200 [:where(&)]:dark:border-zinc-600
            [:where(&)]:overflow-hidden [:where(&)]:shadow-xs
        ',
        TALLKit::roundedSize(size: $size, mode: 'large')
    )"
    :$size
    input:class="border-0 py-1 outline-none ring-0! rounded-none"
    :items:class="TALLKit::paddingInline(size: $size, mode: 'smallest')"
>
    {{ $slot }}

    @isset ($input)
        <x-slot:input>
            {{ $input }}
        </x-slot:input>
    @endisset

    @isset ($empty)
        <x-slot:empty>
            {{ $empty }}
        </x-slot:empty>
    @endisset
</tk:listbox>

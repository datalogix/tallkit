@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'options' => null,
    'scripts' => null,
    'styles' => null,
    'mode' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext($attributes, $label, $id);

$toolbarButtons = [
    ['group' => 'text', 'command' => 'bold', 'icon' => 'bold', 'label' => 'Bold'],
    ['group' => 'text', 'command' => 'italic', 'icon' => 'italic', 'label' => 'Italic'],
    ['group' => 'text', 'command' => 'underline', 'icon' => 'underline', 'label' => 'Underline'],
    ['group' => 'text', 'command' => 'strike', 'icon' => 'strikethrough', 'label' => 'Strikethrough'],
    ['group' => 'heading', 'command' => 'heading1', 'icon' => 'h-1', 'label' => 'Heading 1'],
    ['group' => 'heading', 'command' => 'heading2', 'icon' => 'h-2', 'label' => 'Heading 2'],
    ['group' => 'heading', 'command' => 'heading3', 'icon' => 'h-3', 'label' => 'Heading 3'],
    ['group' => 'script', 'command' => 'subscript', 'icon' => 'subscript', 'label' => 'Subscript'],
    ['group' => 'script', 'command' => 'superscript', 'icon' => 'superscript', 'label' => 'Superscript'],
    ['group' => 'align', 'command' => 'alignLeft', 'icon' => 'align-left', 'label' => 'Align left'],
    ['group' => 'align', 'command' => 'alignCenter', 'icon' => 'align-center', 'label' => 'Align center'],
    ['group' => 'align', 'command' => 'alignRight', 'icon' => 'align-right', 'label' => 'Align right'],
    ['group' => 'align', 'command' => 'alignJustify', 'icon' => 'align-justify', 'label' => 'Justify'],
    ['group' => 'link', 'command' => 'link', 'icon' => 'link', 'label' => 'Link'],
    ['group' => 'list', 'command' => 'bulletList', 'icon' => 'list', 'label' => 'Bullet list'],
    ['group' => 'list', 'command' => 'orderedList', 'icon' => 'list-numbers', 'label' => 'Numbered list'],
    ['group' => 'media', 'command' => 'image', 'icon' => 'photo', 'label' => 'Image'],
    ['group' => 'table', 'command' => 'table', 'icon' => 'table', 'label' => 'Table'],
    ['group' => 'quote', 'command' => 'blockquote', 'icon' => 'blockquote', 'label' => 'Quote'],
    ['group' => 'code', 'command' => 'code', 'icon' => 'code', 'label' => 'Inline code'],
    ['group' => 'code', 'command' => 'codeBlock', 'icon' => 'source-code', 'label' => 'Code block'],
];

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <tk:field.control
        :$size
        :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())"
    >
        <div
            wire:ignore
            x-data="tiptap(
                {{
                    Js::from([
                        'mode' => $mode,
                        'options' => $options ?? [],
                        'scripts' => $scripts ?? [],
                        'styles' => $styles ?? []
                    ])
                }}
            )"
            {{
                TALLKit::attributesAfter($attributes, 'editor:')
                    ->classes('w-full block bg-white text-zinc-700')
            }}
        >
            <textarea
                {{
                    $attributes
                        ->dataKey('control')
                        ->merge([
                            'name' => $name,
                            'id' => $id,
                            'wire:model' => $wireModel,
                            'aria-describedby' => TALLKit::ariaDescribedBy($id, $description, $help, $invalid, $showError),
                            'aria-invalid' => $invalid ? 'true' : null,
                            'data-invalid' => $invalid ? true : null,
                        ])
                        ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes([
                            'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                            'editor:', 'toolbar:',
                        ]))
                        ->class('hidden')
                }}
            >{{ in_livewire() ? null : ($value ?? $slot) }}</textarea>

            <div
                {{
                    TALLKit::attributesAfter($attributes, 'toolbar:')
                        ->classes('flex flex-wrap items-center gap-0.5 border-b border-zinc-200 p-1')
                }}
            >
                @foreach ($toolbarButtons as $toolbarButton)
                    <button
                        type="button"
                        x-show="groups.includes('{{ $toolbarButton['group'] }}')"
                        @click="run('{{ $toolbarButton['command'] }}')"
                        :class="{ 'bg-zinc-200': isActive('{{ $toolbarButton['command'] }}') }"
                        class="rounded p-1.5 hover:bg-zinc-100"
                    >
                        <tk:icon name="{{ $toolbarButton['icon'] }}" tooltip="{{ $toolbarButton['label'] }}" size="sm" />
                    </button>
                @endforeach

                <input
                    type="color"
                    x-show="groups.includes('color')"
                    @input="setColor($event.target.value)"
                    title="Text color"
                    class="size-7 cursor-pointer rounded border border-zinc-300 bg-transparent"
                >
                <input
                    type="color"
                    x-show="groups.includes('color')"
                    @input="setBackgroundColor($event.target.value)"
                    title="Highlight color"
                    class="size-7 cursor-pointer rounded border border-zinc-300 bg-transparent"
                >

                <select
                    x-show="groups.includes('size')"
                    @change="setFontSize($event.target.value)"
                    class="rounded border-zinc-300 bg-transparent text-sm"
                >
                    <option value="">Size</option>
                    <option value="12px">S</option>
                    <option value="16px">M</option>
                    <option value="24px">L</option>
                    <option value="32px">XL</option>
                </select>
            </div>

            <div x-ref="root"></div>
        </div>
    </tk:field.control>
</tk:field.wrapper>

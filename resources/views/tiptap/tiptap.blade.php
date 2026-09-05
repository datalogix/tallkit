@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'options' => null,
    'scripts' => null,
    'styles' => null,
    'mode' => null,
    'version' => null,
    'upload' => [
        'url' => route('tallkit.upload'),
    ],
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label, id: $id);

$toolbarButtons = [
    ['cluster' => 'text', 'group' => 'text', 'command' => 'bold', 'icon' => 'bold', 'label' => 'Bold'],
    ['cluster' => 'text', 'group' => 'text', 'command' => 'italic', 'icon' => 'italic', 'label' => 'Italic'],
    ['cluster' => 'text', 'group' => 'text', 'command' => 'underline', 'icon' => 'underline', 'label' => 'Underline'],
    ['cluster' => 'text', 'group' => 'text', 'command' => 'strike', 'icon' => 'strikethrough', 'label' => 'Strikethrough'],
    ['cluster' => 'heading', 'group' => 'heading', 'command' => 'heading1', 'icon' => 'h-1', 'label' => 'Heading 1'],
    ['cluster' => 'heading', 'group' => 'heading', 'command' => 'heading2', 'icon' => 'h-2', 'label' => 'Heading 2'],
    ['cluster' => 'heading', 'group' => 'heading', 'command' => 'heading3', 'icon' => 'h-3', 'label' => 'Heading 3'],
    ['cluster' => 'script', 'group' => 'script', 'command' => 'subscript', 'icon' => 'subscript', 'label' => 'Subscript'],
    ['cluster' => 'script', 'group' => 'script', 'command' => 'superscript', 'icon' => 'superscript', 'label' => 'Superscript'],
    ['cluster' => 'align', 'group' => 'align', 'command' => 'alignLeft', 'icon' => 'format-align-left', 'label' => 'Align left'],
    ['cluster' => 'align', 'group' => 'align', 'command' => 'alignCenter', 'icon' => 'format-align-center', 'label' => 'Align center'],
    ['cluster' => 'align', 'group' => 'align', 'command' => 'alignRight', 'icon' => 'format-align-right', 'label' => 'Align right'],
    ['cluster' => 'align', 'group' => 'align', 'command' => 'alignJustify', 'icon' => 'format-align-justify', 'label' => 'Justify'],
    ['cluster' => 'insert', 'group' => 'link', 'command' => 'link', 'icon' => 'link', 'label' => 'Link'],
    ['cluster' => 'insert', 'group' => 'media', 'command' => 'image', 'icon' => 'file-image', 'label' => 'Image'],
    ['cluster' => 'insert', 'group' => 'media', 'command' => 'video', 'icon' => 'video', 'label' => 'Video'],
    ['cluster' => 'insert', 'group' => 'table', 'command' => 'table', 'icon' => 'table', 'label' => 'Table'],
    ['cluster' => 'list', 'group' => 'list', 'command' => 'bulletList', 'icon' => 'list', 'label' => 'Bullet list'],
    ['cluster' => 'list', 'group' => 'list', 'command' => 'orderedList', 'icon' => 'list-numbers', 'label' => 'Numbered list'],
    ['cluster' => 'quote', 'group' => 'quote', 'command' => 'blockquote', 'icon' => 'blockquote', 'label' => 'Quote'],
    ['cluster' => 'code', 'group' => 'code', 'command' => 'code', 'icon' => 'code', 'label' => 'Inline code'],
    ['cluster' => 'code', 'group' => 'code', 'command' => 'codeBlock', 'icon' => 'source-code', 'label' => 'Code block'],
];

$toolbarFontSizes = [
    ['value' => '12px', 'label' => 'Small'],
    ['value' => '16px', 'label' => 'Medium'],
    ['value' => '24px', 'label' => 'Large'],
    ['value' => '32px', 'label' => 'Extra large'],
];

$toolbarSections = collect($toolbarButtons)->groupBy('cluster')
    ->map(fn ($buttons) => [
        'type' => 'buttons',
        'groups' => $buttons->pluck('group')->unique()->values()->all(),
        'buttons' => $buttons,
    ])
    ->values()
    ->push(['type' => 'color', 'groups' => ['color'], 'buttons' => null])
    ->push(['type' => 'size', 'groups' => ['size'], 'buttons' => null]);

$toolbarSections = $toolbarSections->map(fn ($section, $index) => [
    ...$section,
    'remainingGroups' => $toolbarSections->slice($index + 1)->flatMap(fn ($s) => $s['groups'])->unique()->values()->all(),
]);

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
                        'version' => $version,
                        'options' => $options ?? [],
                        'scripts' => $scripts ?? [],
                        'styles' => $styles ?? [],
                        'upload' => $upload,
                    ])
                }}
            )"
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'editor:')
                    ->classes(
                        '
                            tk-control-surface
                            tk-control-invalid-border-nested
                            tk-control-focus-ring-nested

                            w-full block overflow-hidden
                            [&_[data-tallkit-control]]:outline-none
                            [&_[data-tallkit-control]]:p-4
                        ',
                        TALLKit::roundedSize(size: $size, mode: 'large'),
                        TALLKit::controlFocusRingNested(color: $color),
                    )
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
                            'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                            'aria-invalid' => $invalid ? 'true' : null,
                            'data-invalid' => $invalid ? true : null,
                        ])
                        ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: ['editor:', 'toolbar']))
                        ->class('hidden')
                }}
            >{{ in_livewire() ? null : ($value ?? $slot) }}</textarea>

            <input type="file" accept="image/*" x-ref="imageInput" @change="insertImage($event)" class="hidden">
            <input type="file" accept="video/*" x-ref="videoInput" @change="insertVideo($event)" class="hidden">

            <div
                {{
                    TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar:')
                        ->classes(
                            '
                                flex flex-wrap items-center gap-x-3 gap-y-1 p-1.5
                                border-b border-zinc-200 dark:border-white/10
                                bg-zinc-50/60 dark:bg-white/5
                            '
                        )
                }}
            >
                @foreach ($toolbarSections as $section)
                    @if ($section['type'] === 'buttons')
                        <div class="flex flex-wrap items-center gap-px">
                            @foreach ($section['buttons'] as $toolbarButton)
                                <tk:button
                                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-button:')
                                        ->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-button-'.$toolbarButton['command'].':')->toArray())
                                        ->classes('rounded-md p-1.5 w-auto h-auto')
                                    "
                                    x-show="groups.includes('{{ $toolbarButton['group'] }}')"
                                    @click="run('{{ $toolbarButton['command'] }}')"
                                    ::data-active="isActive('{{ $toolbarButton['command'] }}')"
                                    :icon="$toolbarButton['icon']"
                                    :tooltip="$toolbarButton['label']"
                                    variant="subtle"
                                />
                            @endforeach
                        </div>
                    @elseif ($section['type'] === 'color')
                        <div x-show="groups.includes('color')" class="flex items-center gap-1">
                            <tk:color-picker
                                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-button:')
                                    ->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-button-text-color:')->toArray())
                                "
                                @input="setColor($event.target.value)"
                                type="button"
                                preview="underline"
                                icon="palette"
                                tooltip="Text color"
                                live="textStyle('color')"
                            />

                            <tk:color-picker
                                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-button:')
                                    ->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-button-highlight-color:')->toArray())
                                "
                                @input="setBackgroundColor($event.target.value)"
                                type="button"
                                preview="underline"
                                icon="highlight"
                                tooltip="Highlight color"
                                live="textStyle('backgroundColor')"
                            />
                        </div>
                    @elseif ($section['type'] === 'size')
                        <div x-show="groups.includes('size')" class="flex items-center gap-1">
                            <tk:dropdown :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-size:')">
                                <tk:dropdown.button
                                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-button:')
                                        ->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-button-size:')->toArray())
                                        ->classes('h-auto w-auto rounded-md px-2 py-1.5 text-xs')
                                    "
                                    variant="subtle"
                                    label="Size"
                                />

                                <tk:menu :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-size-menu:')->classes('w-32')">
                                    @foreach ($toolbarFontSizes as $fontSize)
                                        <tk:menu.item
                                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-size-menu-item:')"
                                            @click="setFontSize('{{ $fontSize['value'] }}')"
                                            label="{{ $fontSize['label'] }}"
                                        />
                                    @endforeach

                                    <tk:menu.separator
                                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-size-menu-separator:')"
                                    />

                                    <tk:menu.item
                                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-size-menu-item:')
                                            ->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-size-menu-item-reset:')->toArray())
                                        "
                                        @click="setFontSize('')"
                                        label="Reset"
                                    />
                                </tk:menu>
                            </tk:dropdown>
                        </div>
                    @endif

                    @if (count($section['remainingGroups']))
                        <div
                            x-show="{{ Js::from($section['groups']) }}.some((g) => groups.includes(g)) && {{ Js::from($section['remainingGroups']) }}.some((g) => groups.includes(g))"
                            {{
                                TALLKit::attributesAfter(attributes: $attributes, prefix: 'toolbar-separator:')
                                    ->classes('h-5 w-px bg-zinc-200 dark:bg-white/10')
                            }}
                        ></div>
                    @endif
                @endforeach
            </div>

            <div x-ref="root"></div>
        </div>
    </tk:field.control>
</tk:field.wrapper>

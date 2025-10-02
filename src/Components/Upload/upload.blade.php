@php
$invalid ??= $name && $errors->has($name);
$files = $getFiles($value);
@endphp

<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
>
    <div
        {{ $buildDataAttribute('control') }}
        class="flex flex-col gap-4"
        x-data="upload({ droppable: @js($droppable ?? true) })"
    >
        <input
            @isset ($name) name="{{ $name }}" @endisset
            @isset ($id) id="{{ $id }}" @endisset
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @if ($multiple) multiple @endif
            {{ $attributes->whereDoesntStartWith([
                    'field:', 'label:', 'information:', 'badge:', 'description:', 'help:', 'error:',
                    'group:', 'prefix:', 'suffix:',
                ])
                ->classes('hidden')
            }}
            type="file"
            x-ref="fileInput"
        />

        <div class="h-48 {{ $multiple ? 'w-full' : 'w-48' }} overflow-hidden rounded-lg relative">
            <div
                wire:loading.flex
                wire:target="{{ $name }}"
                class="absolute z-1 size-full inset-0 flex items-center justify-center bg-white/50 dark:bg-black/70"
            >
                <tk:loading size="lg" />
            </div>

            <tk:button
                :attributes="$attributesAfter('button:')->classes(['hidden' => $files->isNotEmpty() && !$multiple])"
                class="size-full flex-col border-2 border-dashed whitespace-normal"
                label="Arraste ou clique para selecionar"
                icon="cloud-upload-outline"
                icon:size="xl"
                ::class="{'border-blue-400!': dragOver}"
                @click="selectFile"
            />

            @if ($multiple)
                </div>

                <div class="flex gap-4 flex-wrap">
            @endif

            @foreach ($files as $file)
                <div class="{{ $multiple ? 'h-48 w-54' : 'size-full' }} relative flex flex-col rounded-lg overflow-hidden border border-zinc-300 dark:border-white/10 transition-all duration-200">
                    @if ($file->type === 'image')
                        <img src="{{ $file->url }}" class="size-full object-cover" />
                    @elseif ($file->type === 'video')
                        <video src="{{ $file->url }}" controls class="size-full"></video>
                    @elseif ($file->type === 'audio')
                        <audio src="{{ $file->url }}" controls class="h-32 w-full"></audio>
                    @else
                        <tk:icon name="ph:file-{{ $file->type }}" class="size-full" />
                    @endif

                    <tk:dropdown position="bottom">
                        <tk:button icon="dots-vertical" size="xs" class="absolute top-1 right-1" />
                        <tk:menu class="max-w-48">
                            @if (!$multiple)
                                <tk:menu.item
                                    icon="pencil"
                                    label="Edit"
                                    @click="selectFile"
                                />
                            @endif

                            <tk:menu.item
                                icon="trash"
                                label="Remove"
                                @click="removeFile('{{ $name }}')"
                            />
                        </tk:menu>
                    </tk:dropdown>
                </div>
            @endforeach
        </div>
    </div>
</tk:field.wrapper>

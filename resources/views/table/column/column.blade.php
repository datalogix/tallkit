@aware(['dense', 'draggable' => null, 'resizable' => null, 'toggleable' => null, 'pinnable' => null])
@props([
    'name' => null,
    'label' => null,
    'sortable' => null,
    'sortableAction' => null,
    'sortableHref' => null,
    'align' => null,
    'sticky' => null,
    'draggable' => null,
    'resizable' => null,
    'pinnable' => null,
    'hidden' => null,
])
@php

if ($sortable === true && $sortableAction === null && $sortableHref === null) {
    $sortByColumn = (string) ($name ?? $label);
    [$currentSortBy, $currentDirection] = TALLKit::tableSortState();
    $isCurrentColumn = $currentSortBy !== null && $currentSortBy === $sortByColumn;

    $sortable = $isCurrentColumn ? $currentDirection : true;
    $sortableHref = TALLKit::tableSortHref($sortByColumn, $isCurrentColumn && $currentDirection === 'asc' ? 'desc' : 'asc');
}

$key = $attributes->get('data-column-key', $name);
$isColumn = $key !== null && $key !== '';
$isPinned = $sticky === 'left';
$canPin = $isColumn && (bool) $pinnable;
$canDrag = $isColumn && (bool) $draggable && (! $isPinned || $canPin);
$canResize = $isColumn && (bool) $resizable;
$canHide = $isColumn && (bool) $toggleable;

$columnAttributes = TALLKit::tableColumnAttributes(
    name: $isColumn ? (string) $key : null,
    toggleable: (bool) $toggleable,
    hidden: (bool) $hidden,
    sticky: $sticky,
    pinnable: $canPin,
)->mergeDefined([
    'data-column-draggable' => $canDrag ? true : null,
    'data-column-resizable' => $canResize ? true : null,
    'data-column-pinnable' => $canPin ? true : null,
    'x-sort:item' => $canDrag ? Js::from($key) : null,
    'class' => $canResize ? 'relative' : null,
], escape: false);

$gripAttributes = TALLKit::attributesMerge(['x-show' => $canPin ? '! isColumnPinned('.Js::from($key).')' : null]);

$hasHandle = isset($handle) && $handle->hasActualContent();
$hasControls = $canPin || $canDrag || $canResize || $canHide || $hasHandle;

@endphp
<th
    scope="col"
    {{
        $attributes->whereDoesntStartWith(['container:'])
            ->classes([
                'py-4 px-6' => ! $dense,
                'p-2.5' => $dense,
                'tk-table-sticky-column' => $sticky && ! $isPinned,
                '[:where(&)]:font-medium [:where(&)]:text-sm',
                TALLKit::textNeutral(variant: 'strong', prefix: '[:where(&)]:'),
            ])
            ->mergeDefined($columnAttributes, escape: false)
            ->merge([
                'aria-sort' => match ($sortable) {
                    'asc' => 'ascending',
                    'desc' => 'descending',
                    true => 'none',
                    default => false,
                }
            ])
    }}
>
    @if ($hasControls)
        <div class="flex items-center gap-2">
    @endif

    <tk:element
        as="div"
        :attributes="$attributes->prefixed('container:')
            ->classes([
                'flex w-full',
                'group/sortable' => $sortable,
                match ($align) {
                    'center' => 'text-center justify-center',
                    'right' => 'text-end justify-end',
                    default => 'text-start justify-start',
                }
            ])
        "
        :label="$label ?? (filled($name) ? Str::headline($name) : null)"
        :href="$sortable ? $sortableHref : null"
        :action="$sortable ? $sortableAction : null"
        :iconTrailing="match ($sortable) {
            true => 'chevron-up-down',
            'desc' => 'chevron-down',
            'asc' => 'chevron-up',
            default => false
        }"
        icon-trailing:class="opacity-60 group-hover/sortable:opacity-100"
        icon-trailing:size="xs"
    >
        {{ $slot }}
    </tk:element>

    @if ($hasControls)
            @if ($canPin)
                <button
                    type="button"
                    x-on:click="columnTogglePin(@js($key))"
                    x-bind:aria-pressed="isColumnPinned(@js($key))"
                    x-bind:aria-label="isColumnPinned(@js($key)) ? @js(__('Unpin column')) : @js(__('Pin column'))"
                    class="cursor-pointer opacity-60 hover:opacity-100 aria-pressed:opacity-100"
                >
                    <span x-cloak x-show="! isColumnPinned(@js($key))"><tk:icon icon="pin-outline" size="xs" /></span>
                    <span x-cloak x-show="isColumnPinned(@js($key))"><tk:icon icon="pin" size="xs" /></span>
                </button>
            @endif

            @if ($canDrag)
                <span
                    x-sort:handle
                    {{ $gripAttributes->merge(['title' => __('Move column')]) }}
                    aria-hidden="true"
                    class="cursor-grab active:cursor-grabbing opacity-60 hover:opacity-100"
                >
                    <tk:icon icon="grip-vertical" size="xs" />
                </span>
            @endif

            @if ($canResize)
                <span
                    x-on:pointerdown="columnResizeStart(@js($key), $event)"
                    x-on:dblclick="columnResizeFit(@js($key))"
                    x-on:keydown="columnResizeKey(@js($key), $event)"
                    x-bind:aria-valuenow="columnResizeValue(@js($key), $el)"
                    tabindex="0"
                    role="separator"
                    aria-orientation="vertical"
                    aria-valuemin="0"
                    aria-label="{{ __('Resize column') }}"
                    class="
                        absolute inset-y-0 end-0 w-1.5 cursor-col-resize touch-none select-none outline-none
                        hover:bg-zinc-500/30 active:bg-zinc-500/50 dark:hover:bg-white/30 dark:active:bg-white/50
                        focus-visible:bg-blue-700 dark:focus-visible:bg-blue-300
                    "
                ></span>
            @endif

            @if ($canHide)
                <button
                    type="button"
                    x-on:click="columnHide(@js($key))"
                    aria-label="{{ __('Hide column') }}"
                    class="cursor-pointer opacity-60 hover:opacity-100"
                >
                    <tk:icon icon="eye-off" size="xs" />
                </button>
            @endif

            @if ($hasHandle)
                {{ $handle }}
            @endif
        </div>
    @endif
</th>

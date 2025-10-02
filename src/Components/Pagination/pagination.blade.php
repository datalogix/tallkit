@php
$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()"
    : false;
$isPaginator = $paginator instanceof \Illuminate\Contracts\Pagination\Paginator || $paginator instanceof \Illuminate\Contracts\Pagination\CursorPaginator;
$isArrayable = Arr::arrayable($paginator);
@endphp

@if ($total !== false || ($isPaginator && $paginator->hasPages()) || $isArrayable)
    <div {{ $attributes
        ->whereDoesntStartWith(['separator:', 'nav:', 'results:', 'total:', 'pages:', 'page:', 'first-page:', 'prev-page:', 'next:', 'last-page:', 'dots:'])
        ->classes('space-y-3')
    }}>
        <tk:separator :attributes="$attributesAfter('separator:')" />

        @if (isset($results))
            {{ $results($paginator) }}
        @elseif ($paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $paginator->hasPages())
            <nav
                role="navigation"
                aria-label="{{ __('Pagination Navigation') }}"
                {{ $attributesAfter('nav:')->classes('flex gap-1 items-center justify-between') }}
            >
                @if (isset($results))
                    {{ $results($paginator) }}
                @elseif ($total !== false)
                    <tk:text :attributes="$attributesAfter('results:')->classes('hidden sm:block text-gray-700 dark:text-gray-400')">
                        <span>{!! __('Showing') !!}</span>
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        <span>{!! __('to') !!}</span>
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                        <span>{!! __('of') !!}</span>
                        <span class="font-medium">{{ $paginator->total() }}</span>
                        <span>{!! __('results') !!}</span>
                    </tk:text>

                    <tk:text :attributes="$attributesAfter('total:')->classes('sm:hidden text-gray-700 dark:text-gray-400')">
                        <span>{!! __('Total:') !!}</span>
                        <span class="font-medium">{{ $paginator->total() }}</span>
                        <span>{!! trans_choice('pagination.results', $paginator->total()) !!}</span>
                    </tk:text>
                @endif

                <div {{ $attributesAfter('pages:')->classes('flex-1 flex items-center flex-wrap justify-end gap-1 rtl:flex-row-reverse') }}>
                    @if ($firstPage !== false)
                        <tk:pagination.first-page
                            :attributes="$attributesAfter('first-page:')->classes('hidden sm:inline-flex')"
                            :x-on:click="$scrollIntoViewJsSnippet"
                            size="xs"
                        />
                    @endif

                    <tk:pagination.prev-page
                        :attributes="$attributesAfter('prev-page:')"
                        :x-on:click="$scrollIntoViewJsSnippet"
                        size="xs"
                    />

                    @isset ($links)
                        {{ $links($paginator, $elements()) }}
                    @else
                        @foreach ($elements() as $element)
                            @if (is_string($element))
                                <tk:text
                                    :attributes="$attributesAfter('dots:')->classes('px-px hidden lg:inline-flex')"
                                    :label="$element"
                                        aria-disabled="true"
                                />
                            @endif

                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    <tk:pagination.page
                                        :attributes="$attributesAfter('page:')->classes('px-3.5 hidden lg:inline-flex')"
                                        :$page
                                        :$url
                                        size="xs"
                                    />
                                @endforeach
                            @endif
                        @endforeach
                    @endif

                    <tk:pagination.next-page
                        :attributes="$attributesAfter('next-page:')"
                        :x-on:click="$scrollIntoViewJsSnippet"
                        size="xs"
                    />

                    @if ($lastPage !== false)
                        <tk:pagination.last-page
                            :attributes="$attributesAfter('last-page:')->classes('hidden sm:inline-flex')"
                            :x-on:click="$scrollIntoViewJsSnippet"
                            size="xs"
                        />
                    @endif
                </div>
            </nav>
        @elseif ($isPaginator && $paginator->hasPages())
            <nav
                role="navigation"
                aria-label="{{ __('Pagination Navigation') }}"
                {{ $attributesAfter('nav:')->classes('flex gap-1 items-center justify-end') }}
            >
                <tk:pagination.prev-page
                    :attributes="$attributesAfter('prev-page:')"
                    :x-on:click="$scrollIntoViewJsSnippet"
                    size="xs"
                />

                <tk:pagination.next-page
                    :attributes="$attributesAfter('next-page:')"
                    :x-on:click="$scrollIntoViewJsSnippet"
                    size="xs"
                />
            </nav>
        @elseif ($isPaginator)
            <tk:text :attributes="$attributesAfter('total:')->classes('text-gray-700 dark:text-gray-400')">
                <span>{!! __('Total:') !!}</span>
                <span class="font-medium">{{ $paginator->total() }}</span>
                <span>{!! trans_choice('pagination.results', $paginator->total()) !!}</span>
            </tk:text>
        @elseif ($isArrayable)
            <tk:text :attributes="$attributesAfter('total:')->classes('text-gray-700 dark:text-gray-400')">
                <span>{!! __('Total:') !!}</span>
                <span class="font-medium">{{ collect($paginator)->count() }}</span>
                <span>{!! trans_choice('pagination.results', collect($paginator)->count()) !!}</span>
            </tk:text>
        @endif
    </div>
@endif

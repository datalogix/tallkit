@aware(['dense'])

@php
$scrollIntoViewJsSnippet = ($scrollTo !== false) ? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()" : false;
$isPaginator = $paginator instanceof \Illuminate\Contracts\Pagination\Paginator || $paginator instanceof \Illuminate\Contracts\Pagination\CursorPaginator;
$isArrayable = Arr::arrayable($paginator);
$textColors = $classes('text-zinc-700 dark:text-zinc-300');
@endphp

@if ($total !== false || ($isPaginator && $paginator->hasPages()) || $isArrayable)
    <div {{ $attributes
        ->whereDoesntStartWith(['separator:', 'container:', 'nav:', 'results:', 'total:', 'pages:', 'page:', 'first-page:', 'prev-page:', 'next:', 'last-page:', 'dots:'])
        ->classes($textColors)
    }}>
        @if ($separator !== false)
            <tk:separator :attributes="$attributesAfter('separator:')" />
        @endif

        <div {{ $attributesAfter('container:')->classes([
            'py-4 px-6' => !$dense,
            'p-2.5' => $dense,
        ]) }}>
            @if (isset($results))
                {{ $results($paginator) }}
            @elseif ($paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $paginator->hasPages())
                <nav
                    {{ $attributesAfter('nav:')->classes('flex gap-1 items-center justify-between') }}
                    role="navigation"
                    aria-label="{{ __('Pagination Navigation') }}"
                >
                    @if (isset($results))
                        {{ $results($paginator) }}
                    @elseif ($total !== false)
                        <tk:text
                            :attributes="$attributesAfter('results:')->classes('hidden sm:block', $textColors)"
                            :$size
                        >
                            <span>{!! __('Showing') !!}</span>
                            <span class="font-medium">{{ $paginator->firstItem() }}</span>
                            <span>{!! __('to') !!}</span>
                            <span class="font-medium">{{ $paginator->lastItem() }}</span>
                            <span>{!! __('of') !!}</span>
                            <span class="font-medium">{{ $paginator->total() }}</span>
                            <span>{!! __('results') !!}</span>
                        </tk:text>

                        <tk:text
                            :attributes="$attributesAfter('total:')->classes('sm:hidden', $textColors)"
                            :$size
                        >
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
                                :size="$adjustSize()"
                            />
                        @endif

                        <tk:pagination.prev-page
                            :attributes="$attributesAfter('prev-page:')"
                            :x-on:click="$scrollIntoViewJsSnippet"
                            :size="$adjustSize()"
                        />

                        @isset ($links)
                            {{ $links($paginator, $elements()) }}
                        @else
                            @foreach ($elements() as $element)
                                @if (is_string($element))
                                    <tk:text
                                        :attributes="$attributesAfter('dots:')->classes('px-px hidden lg:inline-flex')"
                                        :label="$element"
                                        :size="$adjustSize()"
                                        aria-disabled="true"
                                    />
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        <tk:pagination.page
                                            :attributes="$attributesAfter('page:')->classes('px-3.5 hidden lg:inline-flex')"
                                            :$page
                                            :$url
                                            :size="$adjustSize()"
                                        />
                                    @endforeach
                                @endif
                            @endforeach
                        @endif

                        <tk:pagination.next-page
                            :attributes="$attributesAfter('next-page:')"
                            :x-on:click="$scrollIntoViewJsSnippet"
                            :size="$adjustSize()"
                        />

                        @if ($lastPage !== false)
                            <tk:pagination.last-page
                                :attributes="$attributesAfter('last-page:')->classes('hidden sm:inline-flex')"
                                :x-on:click="$scrollIntoViewJsSnippet"
                                :size="$adjustSize()"
                            />
                        @endif
                    </div>
                </nav>
            @elseif ($isPaginator && $paginator->hasPages())
                <nav
                    {{ $attributesAfter('nav:')->classes('flex gap-1 items-center justify-end') }}
                    role="navigation"
                    aria-label="{{ __('Pagination Navigation') }}"
                >
                    <tk:pagination.prev-page
                        :attributes="$attributesAfter('prev-page:')"
                        :x-on:click="$scrollIntoViewJsSnippet"
                        :size="$adjustSize()"
                    />

                    <tk:pagination.next-page
                        :attributes="$attributesAfter('next-page:')"
                        :x-on:click="$scrollIntoViewJsSnippet"
                        :size="$adjustSize()"
                    />
                </nav>
            @elseif ($isPaginator)
                <tk:text
                    :attributes="$attributesAfter('total:')->classes($textColors)"
                    :$size
                >
                    <span>{!! __('Total:') !!}</span>
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    <span>{!! trans_choice('pagination.results', $paginator->total()) !!}</span>
                </tk:text>
            @elseif ($isArrayable)
                <tk:text
                    :attributes="$attributesAfter('total:')->classes($textColors)"
                    :$size
                >
                    <span>{!! __('Total:') !!}</span>
                    <span class="font-medium">{{ collect($paginator)->count() }}</span>
                    <span>{!! trans_choice('pagination.results', collect($paginator)->count()) !!}</span>
                </tk:text>
            @endif
        </div>
    </div>
@endif

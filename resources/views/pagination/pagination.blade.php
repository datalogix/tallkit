@aware(['dense'])
@props([
    'paginator',
    'scrollTo' => 'body',
    'total' => null,
    'firstPage' => null,
    'lastPage' => null,
    'eachSide' => null,
    'size' => null,
    'separator' => null,
    'dense' => null,
])
@php

$scrollIntoViewJsSnippet = ($scrollTo !== false) ? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()" : false;
$isPaginator = $paginator instanceof \Illuminate\Contracts\Pagination\Paginator || $paginator instanceof \Illuminate\Contracts\Pagination\CursorPaginator;
$isArrayable = Arr::arrayable($paginator);
$textColors = TALLKit::classes('text-zinc-700 dark:text-zinc-300');

@endphp
@if ($total !== false || ($isPaginator && $paginator->hasPages()) || $isArrayable)
    <div {{ $attributes
        ->whereDoesntStartWith([
            'separator:', 'container:', 'nav:', 'results:', 'total:',
            'pages:', 'page:', 'first-page:', 'prev-page:', 'next:', 'last-page:', 'dots:',
        ])
        ->classes($textColors)
    }}>
        @if ($separator !== false)
            <tk:separator :attributes="TALLKit::attributesAfter($attributes, 'separator:')" />
        @endif

        <div {{ TALLKit::attributesAfter($attributes, 'container:')->classes([
            'py-4 px-6' => !$dense,
            'p-2.5' => $dense,
        ]) }}>
            @if (isset($results))
                {{ $results($paginator) }}
            @elseif ($paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $paginator->hasPages())
                <nav
                    {{ TALLKit::attributesAfter($attributes, 'nav:')->classes('flex gap-1 items-center justify-between') }}
                    role="navigation"
                    aria-label="{{ __('Pagination Navigation') }}"
                >
                    @if (isset($results))
                        {{ $results($paginator) }}
                    @elseif ($total !== false)
                        <tk:text
                            :attributes="TALLKit::attributesAfter($attributes, 'results:')->classes('hidden sm:block', $textColors)"
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
                            :attributes="TALLKit::attributesAfter($attributes, 'total:')->classes('sm:hidden', $textColors)"
                            :$size
                        >
                            <span>{!! __('Total:') !!}</span>
                            <span class="font-medium">{{ $paginator->total() }}</span>
                            <span>{!! trans_choice('pagination.results', $paginator->total()) !!}</span>
                        </tk:text>
                    @endif

                    <div {{ TALLKit::attributesAfter($attributes, 'pages:')->classes('
                            flex-1 flex flex-wrap rtl:flex-row-reverse
                            items-center justify-end gap-1
                    ') }}>
                        @if ($firstPage !== false)
                            <tk:pagination.first-page
                                :attributes="TALLKit::attributesAfter($attributes, 'first-page:')->classes('hidden sm:inline-flex')"
                                :x-on:click="$scrollIntoViewJsSnippet"
                                :$size
                            />
                        @endif

                        <tk:pagination.prev-page
                            :attributes="TALLKit::attributesAfter($attributes, 'prev-page:')"
                            :x-on:click="$scrollIntoViewJsSnippet"
                            :$size
                        />
                        @php

                        $paginator->onEachSide($eachSide ?? 3);
                        $window = \Illuminate\Pagination\UrlWindow::make($paginator);
                        $elements = array_filter([
                            $window['first'],
                            is_array($window['slider']) ? '...' : null,
                            $window['slider'],
                            is_array($window['last']) ? '...' : null,
                            $window['last'],
                        ]);

                        @endphp
                        @isset ($links)
                            {{ $links($paginator, $elements) }}
                        @else
                            @foreach ($elements as $element)
                                @if (is_string($element))
                                    <tk:text
                                        :attributes="TALLKit::attributesAfter($attributes, 'dots:')->classes('px-px hidden lg:inline-flex')"
                                        :label="$element"
                                        :$size
                                        aria-disabled="true"
                                    />
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        <tk:pagination.page
                                            :attributes="TALLKit::attributesAfter($attributes, 'page:')->classes('px-3.5 hidden lg:inline-flex')"
                                            :$page
                                            :$url
                                            :$size
                                        />
                                    @endforeach
                                @endif
                            @endforeach
                        @endif

                        <tk:pagination.next-page
                            :attributes="TALLKit::attributesAfter($attributes, 'next-page:')"
                            :x-on:click="$scrollIntoViewJsSnippet"
                            :$size
                        />

                        @if ($lastPage !== false)
                            <tk:pagination.last-page
                                :attributes="TALLKit::attributesAfter($attributes, 'last-page:')->classes('hidden sm:inline-flex')"
                                :x-on:click="$scrollIntoViewJsSnippet"
                                :$size
                            />
                        @endif
                    </div>
                </nav>
            @elseif ($isPaginator && $paginator->hasPages())
                <nav
                    {{ TALLKit::attributesAfter($attributes, 'nav:')->classes('flex gap-1 items-center justify-end') }}
                    role="navigation"
                    aria-label="{{ __('Pagination Navigation') }}"
                >
                    <tk:pagination.prev-page
                        :attributes="TALLKit::attributesAfter($attributes, 'prev-page:')"
                        :x-on:click="$scrollIntoViewJsSnippet"
                        :$size
                    />

                    <tk:pagination.next-page
                        :attributes="TALLKit::attributesAfter($attributes, 'next-page:')"
                        :x-on:click="$scrollIntoViewJsSnippet"
                        :$size
                    />
                </nav>
            @elseif ($isPaginator)
                <tk:text
                    :attributes="TALLKit::attributesAfter($attributes, 'total:')->classes($textColors)"
                    :$size
                >
                    <span>{!! __('Total:') !!}</span>
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    <span>{!! trans_choice('pagination.results', $paginator->total()) !!}</span>
                </tk:text>
            @elseif ($isArrayable)
                <tk:text
                    :attributes="TALLKit::attributesAfter($attributes, 'total:')->classes($textColors)"
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

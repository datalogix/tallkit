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
    'perPage' => null,
    'perPageName' => 'perPage',
])
@php

$scrollIntoViewJsSnippet = ($scrollTo !== false) ? '($el.closest('.Js::from($scrollTo).') || document.querySelector('.Js::from($scrollTo).')).scrollIntoView()' : false;
$isPaginator = $paginator instanceof \Illuminate\Contracts\Pagination\Paginator || $paginator instanceof \Illuminate\Contracts\Pagination\CursorPaginator;
$isArrayable = Arr::arrayable($paginator);
$textColors = TALLKit::classes('text-zinc-700 dark:text-zinc-300');

@endphp
@if ($total !== false || ($isPaginator && $paginator->hasPages()) || $isArrayable)
    <div {{ $attributes
        ->whereDoesntStartWith([
            'separator:', 'container:', 'nav:', 'summary:', 'results:', 'total:', 'per-page:',
            'pages:', 'page:', 'first-page:', 'prev-page:', 'next-page:', 'last-page:', 'dots:',
        ])
        ->classes($textColors)
    }}>
        @if ($separator !== false)
            <tk:separator :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'separator:')" />
        @endif

        <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'container:')->classes([
            'py-4 px-6' => !$dense,
            'p-2.5' => $dense,
        ]) }}>
            @if (isset($results))
                {{ $results($paginator) }}
            @elseif ($paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $paginator->hasPages())
                <nav
                    {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'nav:')->classes('flex gap-1 items-center justify-between') }}
                    role="navigation"
                    aria-label="{{ __('Pagination Navigation') }}"
                >
                    @if (isset($results))
                        {{ $results($paginator) }}
                    @elseif ($total !== false || $perPage)
                        <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'summary:')->classes('flex items-center gap-3') }}>
                            @if ($total !== false)
                                <tk:text
                                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'results:')->classes('hidden sm:block', $textColors)"
                                    :$size
                                >
                                    <span>{!! __('Showing') !!}</span>
                                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                                    <span>{!! __('to') !!}</span>
                                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                                    <span>{!! __('of') !!}</span>
                                    <span class="font-medium">{{ $paginator->total() }}</span>
                                    <span>{!! trans_choice('pagination.results', $paginator->total()) !!}</span>
                                </tk:text>

                                <tk:text
                                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'total:')->classes('sm:hidden', $textColors)"
                                    :$size
                                >
                                    <span>{!! __('Total:') !!}</span>
                                    <span class="font-medium">{{ $paginator->total() }}</span>
                                    <span>{!! trans_choice('pagination.results', $paginator->total()) !!}</span>
                                </tk:text>
                            @endif

                            @if ($perPage)
                                <tk:pagination.per-page
                                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'per-page:')"
                                    :options="$perPage"
                                    :name="$perPageName"
                                    :$size
                                />
                            @endif
                        </div>
                    @endif

                    <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'pages:')->classes('
                            flex-1 flex flex-wrap rtl:flex-row-reverse
                            items-center justify-end gap-1
                    ') }}>
                        @if ($firstPage !== false)
                            <tk:pagination.first-page
                                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'first-page:')->classes('hidden sm:inline-flex')"
                                :x-on:click="$scrollIntoViewJsSnippet"
                                :$size
                            />
                        @endif

                        <tk:pagination.prev-page
                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'prev-page:')"
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
                                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'dots:')->classes('px-px hidden lg:inline-flex')"
                                        :label="$element"
                                        :$size
                                        aria-hidden="true"
                                    />
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $href)
                                        <tk:pagination.page
                                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'page:')->classes('px-3.5 hidden lg:inline-flex')"
                                            :$page
                                            :$href
                                            :$size
                                        />
                                    @endforeach
                                @endif
                            @endforeach
                        @endif

                        <tk:pagination.next-page
                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'next-page:')"
                            :x-on:click="$scrollIntoViewJsSnippet"
                            :$size
                        />

                        @if ($lastPage !== false)
                            <tk:pagination.last-page
                                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'last-page:')->classes('hidden sm:inline-flex')"
                                :x-on:click="$scrollIntoViewJsSnippet"
                                :$size
                            />
                        @endif
                    </div>
                </nav>
            @elseif ($isPaginator && $paginator->hasPages())
                <nav
                    {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'nav:')->classes('flex gap-1 items-center justify-end') }}
                    role="navigation"
                    aria-label="{{ __('Pagination Navigation') }}"
                >
                    <tk:pagination.prev-page
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'prev-page:')"
                        :x-on:click="$scrollIntoViewJsSnippet"
                        :$size
                    />

                    <tk:pagination.next-page
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'next-page:')"
                        :x-on:click="$scrollIntoViewJsSnippet"
                        :$size
                    />
                </nav>
            @elseif ($isPaginator)
                <tk:text
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'total:')->classes($textColors)"
                    :$size
                >
                    <span>{!! __('Total:') !!}</span>
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    <span>{!! trans_choice('pagination.results', $paginator->total()) !!}</span>
                </tk:text>
            @elseif ($isArrayable)
                <tk:text
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'total:')->classes($textColors)"
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

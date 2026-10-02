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

$scrollIntoViewJsSnippet = ($scrollTo !== false) ? '($el.closest('.Js::from($scrollTo).') || document.querySelector('.Js::from($scrollTo).'))?.scrollIntoView()' : false;
$isPaginator = $paginator instanceof \Illuminate\Contracts\Pagination\Paginator || $paginator instanceof \Illuminate\Contracts\Pagination\CursorPaginator;
$isArrayable = Arr::arrayable($paginator);
$textColors = TALLKit::classes(TALLKit::textNeutral());

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
            <tk:separator :attributes="$attributes->prefixed('separator:')" />
        @endif

        <div {{ $attributes->prefixed('container:')->classes([
            'py-4 px-6' => ! $dense,
            'p-2.5' => $dense,
        ]) }}>
            @if (isset($results))
                {{ $results($paginator) }}
            @elseif ($paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $paginator->hasPages())
                <nav
                    {{ $attributes->prefixed('nav:')->classes('flex gap-1 items-center justify-between')->merge(['aria-label' => __('Pagination Navigation')]) }}
                    role="navigation"
                >
                    @if (isset($results))
                        {{ $results($paginator) }}
                    @elseif ($total !== false || $perPage)
                        <div {{ $attributes->prefixed('summary:')->classes('flex items-center gap-3') }}>
                            @if ($total !== false)
                                <tk:text
                                    :attributes="$attributes->prefixed('results:')->classes('hidden sm:block', $textColors)"
                                    :$size
                                >
                                    {{-- One sentence to translate: word by word, a language can't reorder them. --}}
                                    {!! __('Showing :first to :last of :total :results', [
                                        'first' => '<span class="font-medium">'.e($paginator->firstItem()).'</span>',
                                        'last' => '<span class="font-medium">'.e($paginator->lastItem()).'</span>',
                                        'total' => '<span class="font-medium">'.e($paginator->total()).'</span>',
                                        'results' => e(trans_choice('result|results', $paginator->total())),
                                    ]) !!}
                                </tk:text>

                                <tk:text
                                    :attributes="$attributes->prefixed('total:')->classes('sm:hidden', $textColors)"
                                    :$size
                                >
                                    <span>{!! __('Total:') !!}</span>
                                    <span class="font-medium">{{ $paginator->total() }}</span>
                                    <span>{!! trans_choice('result|results', $paginator->total()) !!}</span>
                                </tk:text>
                            @endif

                            @if ($perPage)
                                <tk:pagination.per-page
                                    :attributes="$attributes->prefixed('per-page:')"
                                    :options="$perPage"
                                    :name="$perPageName"
                                    :$size
                                />
                            @endif
                        </div>
                    @endif

                    <div {{ $attributes->prefixed('pages:')->classes('
                            flex-1 flex flex-wrap rtl:flex-row-reverse
                            items-center justify-end gap-1
                    ') }}>
                        @if ($firstPage !== false)
                            <tk:pagination.first-page
                                :attributes="$attributes->prefixed('first-page:')->classes('hidden sm:inline-flex')"
                                :x-on:click="$scrollIntoViewJsSnippet"
                                :$size
                            />
                        @endif

                        <tk:pagination.prev-page
                            :attributes="$attributes->prefixed('prev-page:')"
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
                                        :attributes="$attributes->prefixed('dots:')->classes('px-px hidden lg:inline-flex')"
                                        :label="$element"
                                        :$size
                                        aria-hidden="true"
                                    />
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $href)
                                        <tk:pagination.page
                                            :attributes="$attributes->prefixed('page:')->classes('px-3.5 hidden lg:inline-flex')"
                                            :$page
                                            :$size
                                        />
                                    @endforeach
                                @endif
                            @endforeach
                        @endif

                        <tk:pagination.next-page
                            :attributes="$attributes->prefixed('next-page:')"
                            :x-on:click="$scrollIntoViewJsSnippet"
                            :$size
                        />

                        @if ($lastPage !== false)
                            <tk:pagination.last-page
                                :attributes="$attributes->prefixed('last-page:')->classes('hidden sm:inline-flex')"
                                :x-on:click="$scrollIntoViewJsSnippet"
                                :$size
                            />
                        @endif
                    </div>
                </nav>
            @elseif ($isPaginator && $paginator->hasPages())
                <nav
                    {{ $attributes->prefixed('nav:')->classes('flex gap-1 items-center justify-end')->merge(['aria-label' => __('Pagination Navigation')]) }}
                    role="navigation"
                >
                    <tk:pagination.prev-page
                        :attributes="$attributes->prefixed('prev-page:')"
                        :x-on:click="$scrollIntoViewJsSnippet"
                        :$size
                    />

                    <tk:pagination.next-page
                        :attributes="$attributes->prefixed('next-page:')"
                        :x-on:click="$scrollIntoViewJsSnippet"
                        :$size
                    />
                </nav>
            @elseif ($isPaginator)
                <tk:text
                    :attributes="$attributes->prefixed('total:')->classes($textColors)"
                    :$size
                >
                    <span>{!! __('Total:') !!}</span>
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    <span>{!! trans_choice('result|results', $paginator->total()) !!}</span>
                </tk:text>
            @elseif ($isArrayable)
                <tk:text
                    :attributes="$attributes->prefixed('total:')->classes($textColors)"
                    :$size
                >
                    <span>{!! __('Total:') !!}</span>
                    <span class="font-medium">{{ collect($paginator)->count() }}</span>
                    <span>{!! trans_choice('result|results', collect($paginator)->count()) !!}</span>
                </tk:text>
            @endif
        </div>
    </div>
@endif

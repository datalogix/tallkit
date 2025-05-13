<section class="flex justify-between items-center">
    <div>
        @if ($title || $subtitle)
            <header>
                @if ($title)
                    <div class="text-lg font-medium text-gray-900">
                        {!! __($title) !!}
                    </div>
                @endif

                @if ($subtitle)
                    <div class="text-gray-500">
                        {!! __($subtitle) !!}
                    </div>
                @endif
            </header>
        @endif

        {{ $slot }}
    </div>

    @isset ($actions)
        <footer class="shrink-0">
            {{ $actions }}
        </footer>
    @endisset
</section>

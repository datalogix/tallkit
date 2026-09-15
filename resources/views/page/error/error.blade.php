@props([
    'size' => null,
    'code' => null,
    'title' => null,
    'description' => null,
    'homeUrl' => null,
])
@php

$homeUrl ??= route_detect('home', default: '/');
$title ??= match ((string) $code) {
    '400' => "That request didn't quite work",
    '401' => "Please sign in to continue",
    '403' => "This page isn't available to you",
    '419' => "Your session has expired",
    '429' => "Slow down a little",
    '500' => "Something went wrong on our end",
    '502' => "We're having trouble connecting",
    '503' => "We'll be right back",
    '504' => "That took too long",
    default => "We couldn't find that page",
};

$description ??= match ((string) $code) {
    '400' => "Something about it wasn't understood. Mind going back and trying again?",
    '401' => "You'll need to sign in before you can see this page.",
    '403' => "You don't have permission to view this. If that doesn't sound right, let us know.",
    '419' => "For your security we signed you out after a while. Please refresh the page and try again?",
    '429' => "You've made too many requests in a short time. Give it a moment and try again.",
    '500' => "We've already been notified and we're looking into it. Please try again in a bit.",
    '502' => "We couldn't reach our servers just now. Please try again shortly.",
    '503' => "We're doing a bit of maintenance. Please check back soon.",
    '504' => "The server didn't respond in time. Please try again.",
    default => "It may have been moved, renamed, or the link might be outdated.",
};

@endphp
<div
    {{
        $attributes
            ->whereDoesntStartWith(['code:', 'content:', 'home:'])
            ->classes('relative isolate flex flex-col items-center gap-10 text-center')
    }}
>
    @if ($code)
        <span
            aria-hidden="true"
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'code:')
                    ->classes(
                        '
                            pointer-events-none absolute inset-x-0 top-1/3 -z-10 -translate-y-1/2
                            select-none text-[9rem] font-black leading-none
                            text-zinc-900/4 sm:text-[12rem]
                            dark:text-white/4
                        '
                    )
            }}
        >
            {{ $code }}
        </span>
    @endif

    <tk:content
        :attributes="$attributes->whereStartsWith('content:')"
        :size="TALLKit::adjustSize(size: $size, move: 1)"
        title:class="w-auto"
        :$title
        :$description
    />

    {{ $slot }}

    @if ($homeUrl)
        <tk:button
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'home:')"
            :href="$homeUrl"
            :$size
            label="Go back home"
            variant="accent"
        />
    @endif
</div>

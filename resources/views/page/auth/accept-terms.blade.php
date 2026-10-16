@props([
    'documents' => [],
    'logoutUrl' => null,
    'size' => null,
])
@php

$logoutUrl ??= route_detect(['logout', 'auth.logout'], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['document:', 'content:', 'pdf:', 'link:', 'checkbox:', 'submit:', 'logout:'])"
    title="Accept the terms"
    subtitle="Please read and accept our terms to continue."
    :$size
>
    {{ $slot }}

    @foreach ($documents as $document)
        <div {{ $attributes->prefixed('document:')->classes('space-y-2') }}>
            <tk:heading :$size :label="$document['label']" level="3" />

            @if (filled($document['html'] ?? null))
                <div
                    {{
                        $attributes->prefixed('content:')
                            ->classes('prose prose-sm dark:prose-invert max-w-none max-h-64 overflow-y-auto rounded-lg border border-zinc-200 dark:border-zinc-700 p-4')
                    }}
                    tabindex="0"
                >
                    {!! $document['html'] !!}
                </div>
            @elseif (filled($document['pdf'] ?? null))
                <iframe
                    {{ $attributes->prefixed('pdf:')->classes('h-96 w-full rounded-lg border border-zinc-200 dark:border-zinc-700') }}
                    src="{{ $document['pdf'] }}"
                    title="{{ $document['label'] }}"
                ></iframe>

                <tk:link
                    :attributes="$attributes->prefixed('link:')"
                    :label="__('Open the :document', ['document' => $document['label']])"
                    :href="$document['pdf']"
                    target="_blank"
                    :$size
                />
            @elseif (filled($document['url'] ?? null))
                <tk:link
                    :attributes="$attributes->prefixed('link:')"
                    :label="__('Read the :document', ['document' => $document['label']])"
                    :href="$document['url']"
                    target="_blank"
                    :$size
                />
            @endif

            <tk:checkbox
                :attributes="$attributes->prefixed('checkbox:')"
                :name="'accepted.'.$document['key']"
                :label="__('I have read and accept the :document', ['document' => $document['label']])"
                required
                :$size
            />
        </div>
    @endforeach

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')"
        label="Accept and continue"
        :$size
        variant="accent"
    />

    @if ($logoutUrl)
        <x-slot:append>
            <tk:link
                :attributes="$attributes->prefixed('logout:')"
                label="Log out"
                :href="$logoutUrl"
                :$size
            />
        </x-slot:append>
    @endif
</tk:form.section>

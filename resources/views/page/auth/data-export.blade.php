@props([
    'size' => null,
    'queued' => false,
])
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['submit:'])"
    title="Export your data"
    subtitle="Download a copy of what your account keeps: sign-ins, devices, linked accounts and more."
    :$size
>
    {{ $slot }}

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')"
        :label="$queued ? 'E-mail me a link to my data' : 'Download my data'"
        :$size
        variant="accent"
    />
</tk:form.section>

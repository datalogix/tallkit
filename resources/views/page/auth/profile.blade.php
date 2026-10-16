@props([
    'size' => null,
    'locales' => [],
    'timezones' => [],
    'avatar' => false,
    'avatarDisk' => null,
    'avatarDirectory' => null,
    'avatarMaxSize' => null,
])
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['avatar:', 'name:', 'locale:', 'timezone:', 'submit:'])"
    title="Profile"
    subtitle="How you appear and how the application talks to you."
    :$size
>
    @if ($avatar)
        <tk:upload
            :attributes="$attributes->prefixed('avatar:')"
            variant="avatar"
            label="Photo"
            :$size
            name="avatar"
            accept="image/png,image/jpeg,image/webp"
            :max-size="$avatarMaxSize"
            :disk="$avatarDisk"
            :stored="$avatarDirectory ?? true"
        />
    @endif

    <tk:input
        :attributes="$attributes->prefixed('name:')"
        label="Name"
        :$size
        name="name"
        required
        autocomplete="name"
    />

    @if ($locales)
        <tk:select
            :attributes="$attributes->prefixed('locale:')"
            label="Language"
            placeholder="The default one"
            :$size
            name="locale"
            :options="$locales"
        />
    @endif

    @if ($timezones)
        <tk:select
            :attributes="$attributes->prefixed('timezone:')"
            label="Time zone"
            placeholder="The default one"
            :$size
            name="timezone"
            :options="$timezones"
        />
    @endif

    {{ $slot }}

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')"
        label="Save"
        :$size
        variant="accent"
    />
</tk:form.section>

@props([
    'items' => [],
    'unlink' => 'unlink',
])
<tk:section
    :attributes="$attributes->whereDoesntStartWith(['error:', 'link:', 'unlink:'])->merge(['title' => 'Connected accounts', 'subtitle' => 'Link the accounts you use to sign in, or remove them.', 'separator' => false])"
>
    @error('oauth')
        <tk:alert :attributes="$attributes->prefixed('error:')" type="danger" :$message />
    @enderror

    <div class="divide-y divide-zinc-800/10 dark:divide-white/20">
        @foreach ($items as $item)
            <div class="flex items-center justify-between gap-4 py-3">
                <div class="flex items-center gap-3">
                    <tk:icon :name="TALLKit::oauthIcon($item['provider'])" class="size-5" />

                    <div>
                        <tk:text :label="$item['label']" weight="medium" />
                        <tk:text :label="$item['linked'] ? ($item['email'] ?? 'Linked') : 'Not linked'" variant="subtle" />
                    </div>
                </div>

                @if ($item['linked'])
                    <tk:button
                        :attributes="$attributes->prefixed('unlink:')->merge(['label' => 'Unlink', 'wire:confirm' => __('Unlink your :provider account?', ['provider' => $item['label']])])"
                        :action="$unlink.'('.Js::from($item['provider']).')'"
                        variant="danger"
                    />
                @else
                    <tk:button
                        :attributes="$attributes->prefixed('link:')->merge(['label' => 'Link account'])"
                        :href="$item['linkUrl']"
                        variant="outline"
                    />
                @endif
            </div>
        @endforeach
    </div>
</tk:section>

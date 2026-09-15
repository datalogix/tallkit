<div
    wire:ignore
    x-data="toast"
    tabindex="-1"
    {{
        $attributes
            ->whereDoesntStartWith([
                'position:', 'container:', 'area:', 'content:',
                'icon', 'title:', 'message:', 'actions:', 'close:',
                'progress:',
            ])
            ->classes('fixed inset-0 overflow-hidden pointer-events-none z-9999999')
    }}
>
    @foreach (['top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right'] as $position)
        <div
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'position:')->classes(
                    'absolute flex flex-col',
                    str_contains($position, 'top') ? 'flex-col-reverse' : null,
                    match ($position) {
                        'top-left' => 'top-2 left-2 items-start',
                        'top-center' => 'top-2 left-1/2 -translate-x-1/2 items-center',
                        'top-right' => 'top-2 right-2 items-end',
                        'bottom-left' => 'bottom-2 left-2 items-start',
                        'bottom-center' => 'bottom-2 left-1/2 -translate-x-1/2 items-center',
                        'bottom-right' => 'bottom-2 right-2 items-end',
                    },
                )
            }}
        >
            <template
                x-for="toast in getToastsByPosition('{{ $position }}')"
                :key="toast.id"
            >
                <tk:transition
                    aria-atomic="true"
                    ::role="toast.type === 'error' ? 'alert' : 'status'"
                    ::aria-live="toast.type === 'error' ? 'assertive' : 'polite'"
                    :animation="match ($position) {
                        'top-left', 'bottom-left' => 'slide-right-full',
                        'top-right', 'bottom-right' => 'slide-left-full',
                        'top-center' => 'slide-down-full',
                        'bottom-center' => 'slide-up-full',
                        default => 'none',
                    }"
                    :attributes="
                        TALLKit::attributesAfter(attributes: $attributes, prefix: 'container:')->classes(
                            '
                                m-1 shadow-lg rounded-xl border
                                relative overflow-hidden
                                flex items-start pointer-events-auto
                                touch-pan-y
                            '
                        )
                    "
                    ::style="toast.swipe ? {
                        transform: `translateX(${toast.currentX}px)`,
                        opacity: 1 - Math.min(Math.abs(toast.currentX) / 150, 1)
                    } : {}"
                    ::class="{
                        'gap-3 max-w-xs p-3 text-[11px] **:data-tallkit-icon:mt-0.5 **:data-tallkit-icon:size-3': toast.size === 'xs',
                        'gap-3 max-w-xs p-3 text-xs **:data-tallkit-icon:mt-px **:data-tallkit-icon:size-3.5': toast.size === 'sm',
                        'gap-4 max-w-sm p-4 text-sm **:data-tallkit-icon:mt-0.5 **:data-tallkit-icon:size-4': !toast.size || toast.size === 'md',
                        'gap-4 max-w-sm p-4 text-base **:data-tallkit-icon:mt-1 **:data-tallkit-icon:size-4.5': toast.size === 'lg',
                        'gap-5 max-w-md p-5 text-lg **:data-tallkit-icon:mt-1 **:data-tallkit-icon:size-5': toast.size === 'xl',
                        'gap-5 max-w-md p-5 text-xl **:data-tallkit-icon:mt-1 **:data-tallkit-icon:size-5.5': toast.size === '2xl',
                        'gap-6 max-w-lg p-6 text-2xl **:data-tallkit-icon:mt-1 **:data-tallkit-icon:size-6': toast.size === '3xl',

                        'bg-white dark:bg-zinc-700 border-zinc-200 dark:border-white/10': !toast.invert,
                        'bg-zinc-700 dark:bg-white border-white/10 dark:border-zinc-200': toast.invert,
                    }"
                    x-show="toast.visible"
                    @click.stop="toast.swiping && $event.preventDefault()"
                    @mouseenter="toast.pauseOnHover && toast.pause('hover')"
                    @mouseleave="toast.pauseOnHover && toast.resume('hover')"
                    @pointerdown="toast.swipe && toast.onPointerDown($event)"
                    @pointermove="toast.swipe && toast.onPointerMove($event)"
                    @pointerup="toast.swipe && toast.onPointerUp($event)"
                >
                    <tk:icon
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-success:')->getAttributes())->classes('shrink-0')"
                        ::class="toast.invert ? 'text-green-400 dark:text-green-500' : 'text-green-500 dark:text-green-400'"
                        x-show="toast.type === 'success'"
                        name="check-circle"
                    />
                    <tk:icon
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-info:')->getAttributes())->classes('shrink-0')"
                        ::class="toast.invert ? 'text-blue-400 dark:text-blue-500' : 'text-blue-500 dark:text-blue-400'"
                        x-show="toast.type === 'info'"
                        name="info"
                    />
                    <tk:icon
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-error:')->getAttributes())->classes('shrink-0')"
                        ::class="toast.invert ? 'text-red-400 dark:text-red-500' : 'text-red-500 dark:text-red-400'"
                        x-show="toast.type === 'error'"
                        name="cancel"
                    />
                    <tk:icon
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-warning:')->getAttributes())->classes('shrink-0')"
                        ::class="toast.invert ? 'text-amber-400 dark:text-amber-500' : 'text-amber-500 dark:text-amber-400'"
                        x-show="toast.type === 'warning'"
                        name="warning"
                    />
                    <tk:loading
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-loading:')->getAttributes())->classes('shrink-0')"
                        ::class="toast.invert ? 'text-zinc-400 dark:text-zinc-500' : 'text-zinc-500 dark:text-zinc-400'"
                        x-show="toast.type === 'loading'"
                    />
                    <div class="flex-1 flex flex-col gap-2">
                        <div
                            {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'title:')->classes('font-medium') }}
                            :class="toast.invert ? 'text-white dark:text-zinc-800' : 'text-zinc-800 dark:text-white'"
                            x-html="toast.title || toast.message"
                        ></div>
                        <div
                            {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'message:')->classes('font-normal') }}
                            :class="toast.invert ? 'text-zinc-400 dark:text-zinc-500' : 'text-zinc-500 dark:text-zinc-400'"
                            x-show="toast.title && toast.message"
                            x-html="toast.message"
                        ></div>
                        <div
                            x-show="toast.actions && toast.actions.length"
                            class="flex items-center gap-1 mt-2"
                        >
                            <template x-for="(action, index) in (toast.actions ?? [])" :key="index">
                                <div class="flex items-center">
                                    <a
                                        x-show="action.href"
                                        :href="action.href"
                                        :target="action.target ?? null"
                                        x-text="action.label"
                                        {{
                                            TALLKit::attributesAfter(attributes: $attributes, prefix: 'actions:')->classes(
                                                'inline-flex h-8 items-center justify-center rounded-md px-3 text-sm font-medium whitespace-nowrap'
                                            )
                                        }}
                                        :class="toast.invert
                                            ? 'bg-white/10 hover:bg-white/20 text-white dark:bg-zinc-800/5 dark:hover:bg-zinc-800/10 dark:text-zinc-800'
                                            : 'bg-zinc-800/5 hover:bg-zinc-800/10 text-zinc-800 dark:bg-white/10 dark:hover:bg-white/20 dark:text-white'"
                                    ></a>
                                    <button
                                        x-show="!action.href"
                                        type="button"
                                        @click="action.run()"
                                        :disabled="action.loading"
                                        {{
                                            TALLKit::attributesAfter(attributes: $attributes, prefix: 'actions:')->classes(
                                                'inline-flex h-8 items-center justify-center gap-2 rounded-md px-3 text-sm font-medium whitespace-nowrap disabled:opacity-50 disabled:cursor-default'
                                            )
                                        }}
                                        :class="toast.invert
                                            ? 'bg-white/10 hover:bg-white/20 text-white dark:bg-zinc-800/5 dark:hover:bg-zinc-800/10 dark:text-zinc-800'
                                            : 'bg-zinc-800/5 hover:bg-zinc-800/10 text-zinc-800 dark:bg-white/10 dark:hover:bg-white/20 dark:text-white'"
                                    >
                                        <span x-show="!action.loading" x-text="action.label"></span>
                                        <tk:loading x-show="action.loading" ::class="'size-4'" />
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    <tk:button
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'close:')"
                        ::class="toast.invert ? 'text-white hover:text-zinc-300 dark:text-zinc-800 dark:hover:text-zinc-500' : ''"
                        x-on:click="removeToast(toast.id)"
                        icon="close"
                        variant="none"
                        tooltip="Close"
                    />
                    <div
                        x-show="toast.progress && toast.duration"
                        {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'progress:')->classes('h-full absolute inset-0 pointer-events-none origin-left') }}
                        :class="toast.invert ? 'bg-black/15 dark:bg-black/5' : 'bg-black/2 dark:bg-black/5'"
                        :style="toast.progress ? { transform: `scaleX(${toast.progressValue})` } : {}"
                    ></div>
                </tk:transition>
            </template>
        </div>
    @endforeach
</div>

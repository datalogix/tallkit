<div
    x-data="toast"
    tabindex="-1"
    {{ $attributes
        ->whereDoesntStartWith(['position:', 'container:', 'area:', 'content:', 'icon-', 'heading:', 'text:', 'actions:', 'close:'])
        ->classes('fixed inset-0 overflow-hidden pointer-events-none z-9999999')
    }}
>
    <template x-for="position in positions" :key="position">
        <div
            {{ $attributesAfter('position:')->classes('absolute pointer-events-auto flex flex-col') }}
            :class="{
                'flex-col-reverse': position.includes('top'),
                'top-2 left-2 items-start': position === 'top-left',
                'top-2 left-1/2 -translate-x-1/2 items-center': position === 'top-center',
                'top-2 right-2 items-end': position === 'top-right',
                'bottom-2 left-2 items-start': position === 'bottom-left',
                'bottom-2 left-1/2 -translate-x-1/2 items-center': position === 'bottom-center',
                'bottom-2 right-2 items-end': position === 'bottom-right',
            }"
        >
            <template
                x-for="toast in getToastsByPosition(position)"
                :key="toast.id"
            >
                <div
                    {{ $attributesAfter('container:')->classes('
                        max-w-sm m-1 p-2
                        rounded-xl shadow-lg
                        bg-white dark:bg-zinc-700
                        border border-zinc-200 dark:border-white/10
                    ') }}
                    x-show="toast.visible"
                    x-bind="{
                        'x-transition:enter': 'transition ease-out duration-350',
                        'x-transition:enter-start': {
                            'top-left': '-translate-x-full opacity-0',
                            'top-center': '-translate-y-full opacity-0',
                            'top-right': 'translate-x-full opacity-0',
                            'bottom-left': '-translate-x-full opacity-0',
                            'bottom-center': 'translate-y-full opacity-0',
                            'bottom-right': 'translate-x-full opacity-0',
                        }[position],
                        'x-transition:enter-end': 'translate-0 opacity-100',
                        'x-transition:leave': 'transition ease-in duration-200',
                        'x-transition:leave-start': 'translate-0 opacity-100',
                        'x-transition:leave-end': {
                            'top-left': '-translate-x-full opacity-0',
                            'top-center': '-translate-y-full opacity-0',
                            'top-right': 'translate-x-full opacity-0',
                            'bottom-left': '-translate-x-full opacity-0',
                            'bottom-center': 'translate-y-full opacity-0',
                            'bottom-right': 'translate-x-full opacity-0',
                        }[position],
                    }"
                >
                    <div {{ $attributesAfter('area:')->classes('flex items-start gap-4') }}>
                        <div {{ $attributesAfter('content:')->classes('flex-1 py-1.5 ps-2.5 flex gap-2') }}>
                            <tk:icon
                                :attributes="$attributesAfter('icon-success:')->classes('mt-0.5 shrink-0 text-green-500 dark:text-green-400')"
                                x-show="toast.type === 'success'"
                                name="check-circle"
                                size="xs"
                            />
                            <tk:icon
                                :attributes="$attributesAfter('icon-info:')->classes('mt-0.5 shrink-0 text-blue-500 dark:text-blue-400')"
                                x-show="toast.type === 'info'"
                                name="info"
                                size="xs"
                            />
                            <tk:icon
                                :attributes="$attributesAfter('icon-danger:')->classes('mt-0.5 shrink-0 text-red-500 dark:text-red-400')"
                                x-show="toast.type === 'danger'"
                                name="cancel"
                                size="xs"
                            />
                            <tk:icon
                                :attributes="$attributesAfter('icon-warning:')->classes('mt-0.5 shrink-0 text-amber-500 dark:text-amber-400')"
                                x-show="toast.type === 'warn'"
                                name="warning"
                                size="xs"
                            />
                            <div class="flex flex-col gap-2">
                                <div
                                    {{ $attributesAfter('heading:')->classes('text-sm font-medium text-zinc-800 dark:text-white') }}
                                    x-html="toast.heading || toast.text"
                                ></div>
                                <div
                                    {{ $attributesAfter('text:')->classes('text-sm font-normal text-zinc-500 dark:text-zinc-300') }}
                                    x-show="toast.heading && toast.text"
                                    x-html="toast.text"
                                ></div>
                            </div>
                        </div>
                        <div {{ $attributesAfter('actions:')->classes('flex items-center gap-2') }}>
                            <tk:button
                                :attributes="$attributesAfter('close:')"
                                x-on:click="removeToast(toast.id)"
                                icon="times"
                                variant="subtle"
                                size="xs"
                            />
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </template>
</div>

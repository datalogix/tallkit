<div
    x-data="toast"
    {{ $attributes
        ->whereDoesntStartWith(['position:', 'container:', 'area:', 'content:', 'icon-', 'heading:', 'text:', 'actions:', 'close:'])
        ->classes('fixed inset-0 overflow-hidden pointer-events-none z-9999999')
    }}
>
    <template x-for="position in positions" :key="position">
        <div
            {{ $attributesAfter('position:')->classes('absolute space-y-2 pointer-events-auto') }}
            :class="{
                'top-5 left-5 items-start': position === 'top-left',
                'top-5 right-5 items-end': position === 'top-right',
                'bottom-5 left-5 items-start': position === 'bottom-left',
                'bottom-5 right-5 items-end': position === 'bottom-right',
            }"
        >
            <template
                x-for="toast in getToastsByPosition(position)"
                :key="toast.id"
            >
                <div
                    {{ $attributesAfter('container:')->classes('max-w-sm p-2 rounded-xl shadow-lg bg-white border border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600') }}
                    x-show="toast.visible"
                    x-bind="{
                        'x-transition:enter': 'transition ease-out duration-300',
                        'x-transition:enter-start': position.includes('left') ? '-translate-x-full opacity-0' : 'translate-x-full opacity-0',
                        'x-transition:enter-end': 'translate-x-0 opacity-100',
                        'x-transition:leave': 'transition ease-in duration-300',
                        'x-transition:leave-start': 'translate-x-0 opacity-100',
                        'x-transition:leave-end': position.includes('left') ? '-translate-x-full opacity-0' : 'translate-x-full opacity-0',
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
                        <div {{ $attributesAfter('actions:')->classes('flex items-center') }}>
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

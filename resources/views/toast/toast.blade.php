<div
    x-data="toast"
    tabindex="-1"
    {{ $attributes
        ->whereDoesntStartWith([
            'position:', 'container:', 'area:', 'content:',
            'icon', 'title:', 'message:', 'actions:', 'close:',
            'progress:',
        ])
        ->classes('fixed inset-0 overflow-hidden pointer-events-none z-9999999')
    }}
>
    <template x-for="position in positions" :key="position">
        <div
            {{ TALLKit::attributesAfter($attributes, 'position:')->classes('absolute flex flex-col') }}
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
                    {{ TALLKit::attributesAfter($attributes, 'container:')->classes(
                        '
                            m-1 shadow-lg rounded-xl
                            bg-white dark:bg-zinc-700
                            border border-zinc-200 dark:border-white/10
                            relative overflow-hidden
                            flex items-start pointer-events-auto
                            touch-pan-y
                        '
                    ) }}
                    :style="toast.swipeEnabled ? {
                        transform: `translateX(${toast.currentX}px)`,
                        opacity: 1 - Math.min(Math.abs(toast.currentX) / 150, 1)
                    } : {}"
                    :class="{
                        'gap-3 max-w-xs p-3 text-[11px] **:data-tallkit-icon:mt-0.5 **:data-tallkit-icon:size-3': toast.size === 'xs',
                        'gap-3 max-w-xs p-3 text-xs **:data-tallkit-icon:mt-px **:data-tallkit-icon:size-3.5': toast.size === 'sm',
                        'gap-4 max-w-sm p-4 text-sm **:data-tallkit-icon:mt-0.5 **:data-tallkit-icon:size-4': !toast.size || toast.size === 'md',
                        'gap-4 max-w-sm p-4 text-base **:data-tallkit-icon:mt-1 **:data-tallkit-icon:size-4.5': toast.size === 'lg',
                        'gap-5 max-w-md p-5 text-lg **:data-tallkit-icon:mt-1 **:data-tallkit-icon:size-5': toast.size === 'xl',
                        'gap-5 max-w-md p-5 text-xl **:data-tallkit-icon:mt-1 **:data-tallkit-icon:size-5.5': toast.size === '2xl',
                        'gap-6 max-w-lg p-6 text-2xl **:data-tallkit-icon:mt-1 **:data-tallkit-icon:size-6': toast.size === '3xl',
                    }"
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
                    @click.stop="toast.swiping && $event.preventDefault()"
                    @mouseenter="toast.pauseOnHover && toast.pause()"
                    @mouseleave="toast.pauseOnHover && toast.resume()"
                    @pointerdown="toast.swipe && toast.onPointerDown($event)"
                    @pointermove="toast.swipe && toast.onPointerMove($event)"
                    @pointerup="toast.swipe && toast.onPointerUp($event)"
                >
                    <tk:icon
                        :attributes="TALLKit::attributesAfter($attributes, 'icon:')->merge(TALLKit::attributesAfter($attributes, 'icon-success:')->getAttributes())->classes('shrink-0 text-green-500 dark:text-green-400')"
                        x-show="toast.type === 'success'"
                        name="check-circle"
                    />
                    <tk:icon
                        :attributes="TALLKit::attributesAfter($attributes, 'icon:')->merge(TALLKit::attributesAfter($attributes, 'icon-info:')->getAttributes())->classes('shrink-0 text-blue-500 dark:text-blue-400')"
                        x-show="toast.type === 'info'"
                        name="info"
                    />
                    <tk:icon
                        :attributes="TALLKit::attributesAfter($attributes, 'icon:')->merge(TALLKit::attributesAfter($attributes, 'icon-danger:')->getAttributes())->classes('shrink-0 text-red-500 dark:text-red-400')"
                        x-show="toast.type === 'danger'"
                        name="cancel"
                    />
                    <tk:icon
                        :attributes="TALLKit::attributesAfter($attributes, 'icon:')->merge(TALLKit::attributesAfter($attributes, 'icon-warning:')->getAttributes())->classes('shrink-0 text-amber-500 dark:text-amber-400')"
                        x-show="toast.type === 'warn'"
                        name="warning"
                    />
                    <div class="flex-1 flex flex-col gap-2">
                        <div
                            {{ TALLKit::attributesAfter($attributes, 'title:')->classes('font-medium text-zinc-800 dark:text-white') }}
                            x-html="toast.title || toast.message"
                        ></div>
                        <div
                            {{ TALLKit::attributesAfter($attributes, 'message:')->classes('font-normal text-zinc-500 dark:text-zinc-300') }}
                            x-show="toast.title && toast.message"
                            x-html="toast.message"
                        ></div>
                    </div>
                    <tk:button
                        :attributes="TALLKit::attributesAfter($attributes, 'close:')"
                        x-on:click="removeToast(toast.id)"
                        icon="times"
                        variant="none"
                    />
                    <div
                        x-show="progress"
                        {{ TALLKit::attributesAfter($attributes, 'progress:')->classes('bg-black/5 dark:bg-black/10 h-full absolute inset-0 pointer-events-none origin-left') }}
                        :style="toast.progress ? { transform: `scaleX(${toast.progressValue})` } : {}"
                    ></div>
                </div>
            </template>
        </div>
    </template>
</div>

<div
    class="w-0 h-0 overflow-hidden"
    x-data="toast"
    @toast.document="open($event.detail)"
>
    <div
        popover="manual"
        data-position="bottom right"
        class="m-0 p-6 bg-transparent duration-500
            opacity-0
            translate-0
            transition-all
            &:translate-0
            transition-discrete

            [&:is(:popover-open)]:opacity-100
            [&:is(:popover-open)]:translate-0
            [&:is(:popover-open)]:transition-all
            [&:is(:popover-open)]:transition-discrete

            [@starting-style]:[&:is(:popover-open)]:opacity-0
            [@starting-style]:[&:is(:popover-open)]:-translate-x-5
        "
        aria-atomic="true"
    >
        <div class="max-w-sm p-2 rounded-xl shadow-lg bg-white border border-zinc-200 border-b-zinc-300/80 dark:bg-zinc-700 dark:border-zinc-600">
            <div class="flex items-start gap-4">
                <div class="flex-1 py-1.5 ps-2.5 flex gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="block shrink-0 mt-0.5 size-4 text-lime-600 dark:text-lime-400">
                        <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm3.844-8.791a.75.75 0 0 0-1.188-.918l-3.7 4.79-1.649-1.833a.75.75 0 1 0-1.114 1.004l2.25 2.5a.75.75 0 0 0 1.15-.043l4.25-5.5Z" clip-rule="evenodd"></path>
                    </svg>

                    <div>
                        <tk:heading size="sm" class="[&:not(:empty)]:mb-2" x-html="heading" />
                        <tk:text x-html="text" />
                    </div>
                </div>

                <div class="flex items-center">
                    <button type="button" @click="close" class="inline-flex items-center font-medium justify-center gap-2 truncate disabled:opacity-50 dark:disabled:opacity-75 disabled:cursor-default h-8 text-sm rounded-md w-8 bg-transparent hover:bg-zinc-800/5 dark:hover:bg-white/15 text-zinc-400 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-white" as="button">
                        <div>
                            <svg class="[:where(&amp;)]:size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"></path>
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>



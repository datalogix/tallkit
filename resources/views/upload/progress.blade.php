<template x-if="multiple && activeFiles.length > 1">
    <div class="h-1 w-full rounded-full overflow-hidden bg-zinc-200 dark:bg-white/10 mb-4">
        <div
            class="h-full bg-blue-700 dark:bg-blue-300 transition-[width] duration-150"
            :style="{ width: aggregateProgress + '%' }"
        ></div>
    </div>
</template>

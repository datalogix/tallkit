<tk:dropdown>
    <tk:button
        icon="bell-outline"
        variant="subtle"
        icon-dot="13"
        icon-dot:class="bg-blue-500!"
        class="[&[data-active]]:border [&[data-active]]:border-blue-500"
        ::data-active="opened"
    />

    <tk:popover class="max-w-md w-full p-0">
        <tk:section
            title="Notificações"
            size="sm"
            class="space-y-0"
            header:class="p-3"
            content:class="max-h-120 overflow-y-auto"
        >
            <x-slot:actions>
                <tk:button
                    variant="none"
                    label="Mark all as read"
                />
            </x-slot:actions>

            <div class="m-1">
                @foreach (range(1, 5) as $x)
                    <div>
                        <tk:heading
                            label="Hoje"
                            size="xs"
                            class="px-3 text-zinc-500 dark:text-zinc-400"
                        />

                        @foreach (range(1, 5) as $i)
                            <a href="#" class="flex gap-4 p-3 rounded-lg hover:bg-zinc-800/5 dark:hover:bg-zinc-800/80 transition group">
                                <div class="shrink-0">
                                    <tk:avatar :user="false" square icon="plus" />
                                </div>
                                <div class="space-y-2 flex-1">
                                    <tk:text>Deployment 2f581d6 finished on Da sa dtds adjslk fdsj lk fslkalogix / aemc.on-forge.com</tk:text>
                                    <tk:text variant="subtle">1 day ago</tk:text>
                                </div>
                                <div class="shrink-0 w-20 ms-auto flex justify-end">
                                    <div class="size-3 bg-green-500 rounded-full block group-hover:hidden"></div>
                                    <tk:button.group class="hidden group-hover:flex">
                                        <tk:button icon="trash-outline" tooltip="Delete notification" />
                                        <tk:button icon="check-circle-outline" tooltip="Mark as read" />
                                        <tk:button icon="message-badge-outline" tooltip="Mark as unread" />
                                    </tk:button.group>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>
         </tk:section>
    </tk:popover>
</tk:dropdown>

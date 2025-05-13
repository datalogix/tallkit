<?php

namespace TALLKit\Components\Card;

use TALLKit\View\BladeComponent;

class Card extends BladeComponent
{
    public function render()
    {
        return <<<'BLADE'
            <div class="m-auto max-w-full max-lg:min-w-fit flex justify-center">
                <div class="w-full">
                    <div class="
                        bg-white dark:bg-white/10
                        border border-zinc-200 dark:border-white/10
                        [:where(&amp;)]:p-6
                        [:where(&amp;)]:rounded-xl
                        space-y-6
                    ">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        BLADE;
    }
}

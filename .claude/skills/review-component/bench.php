<?php

// Run from ../app: XDEBUG_MODE=off php -dopcache.enable_cli=1 <this file> '<tk:button>Save</tk:button>' ...
require getcwd().'/vendor/autoload.php';

(require getcwd().'/bootstrap/app.php')->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (array_slice($argv, 1) as $template) {
    for ($i = 0; $i < 200; $i++) {
        Illuminate\Support\Facades\Blade::render($template);
    }

    $best = INF;

    for ($run = 0; $run < 5; $run++) {
        $start = hrtime(true);

        for ($i = 0; $i < 1000; $i++) {
            Illuminate\Support\Facades\Blade::render($template);
        }

        $best = min($best, (hrtime(true) - $start) / 1e9);
    }

    echo number_format($best, 3, '.', ''), " ms  {$template}\n";
}

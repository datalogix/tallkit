<?php

// Run from ../app: XDEBUG_MODE=off php <this file> ../tallkit/docs/<component>.md
require getcwd().'/vendor/autoload.php';

(require getcwd().'/bootstrap/app.php')->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

preg_match_all('/```blade\n(.*?)```/s', file_get_contents($argv[1]), $matches);

$failed = false;

foreach ($matches[1] as $index => $template) {
    try {
        Illuminate\Support\Facades\Blade::render($template);

        echo 'ok    example ', $index + 1, "\n";
    } catch (Throwable $exception) {
        if (str_contains($exception->getMessage(), 'Undefined variable')) {
            echo 'skip  example ', $index + 1, ': ', $exception->getMessage(), "\n";

            continue;
        }

        $failed = true;

        echo 'FAIL  example ', $index + 1, ': ', $exception->getMessage(), "\n";
    }
}

exit($failed ? 1 : 0);

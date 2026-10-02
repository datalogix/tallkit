<?php

namespace TALLKit\View;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class BladeDirectives
{
    public static function register()
    {
        Blade::directive('scope', [static::class, 'scope']);
        Blade::directive('endscope', [static::class, 'endscope']);
    }

    public static function scope($expression)
    {
        $arguments = array_pad(static::splitArguments($expression), 2, '()');

        // @scope('name', $item, $index): without parentheses, everything after the name is a parameter.
        [$name, $functionArguments, $functionUses] = Str::startsWith($arguments[1], '(')
            ? array_pad($arguments, 3, null)
            : [$arguments[0], '('.implode(', ', array_slice($arguments, 1)).')', null];

        $functionUses = array_filter(array_map('trim', explode(',', trim($functionUses ?? '', '()'))), 'strlen');
        $functionUses = implode(', ', array_unique([...$functionUses, '$__env', '$__bladeCompiler']));

        return "<?php \$__bladeCompiler = \$__bladeCompiler ?? null; \$__env->slot({$name}, function {$functionArguments} use ({$functionUses}) { ?>";
    }

    protected static function splitArguments(string $expression): array
    {
        $parts = [''];
        $depth = 0;

        foreach (array_slice(token_get_all('<?php '.$expression), 1) as $token) {
            $text = is_array($token) ? $token[1] : $token;

            if ($text === ',' && $depth === 0) {
                $parts[] = '';

                continue;
            }

            if (in_array($text, ['(', '[', '{'], true)) {
                $depth++;
            } elseif (in_array($text, [')', ']', '}'], true)) {
                $depth--;
            }

            $parts[array_key_last($parts)] .= $text;
        }

        return array_map('trim', $parts);
    }

    public static function endscope()
    {
        return '<?php }); ?>';
    }
}

<?php

namespace TALLKit\View;

use Illuminate\Support\Facades\Blade;

class BladeDirectives
{
    public static function register()
    {
        Blade::directive('bind', [static::class, 'bind']);
        Blade::directive('endbind', [static::class, 'endbind']);
        Blade::directive('scope', [static::class, 'scope']);
        Blade::directive('endscope', [static::class, 'endscope']);
    }

    public static function bind($expression)
    {
        return '<?php app(\'tallkit\')->bind('.$expression.'); ?>';
    }

    public static function endbind()
    {
        return '<?php app(\'tallkit\')->endBind(); ?>';
    }

    public static function scope($expression)
    {
        // Split the expression by `top-level` commas (not in parentheses)
        $directiveArguments = preg_split("/,(?![^\(\(]*[\)\)])/", $expression);
        $directiveArguments = array_pad(array_map('trim', $directiveArguments), 2, '()');

        // Prepare arguments to uses
        if (! str_starts_with($directiveArguments[1], '(')) {
            $ar = $directiveArguments;
            $directiveArguments = [];
            $directiveArguments[] = $ar[0];
            unset($ar[0]);
            $directiveArguments[] = '('.implode(', ', $ar).')';
        }

        // Ensure that the directive's arguments array has 3 elements - otherwise fill with `null`
        $directiveArguments = array_pad($directiveArguments, 3, null);

        // Extract values from the directive's arguments array
        [$name, $functionArguments, $functionUses] = $directiveArguments;

        // Connect the arguments to form a correct function declaration
        if ($functionArguments) {
            $functionArguments = "function {$functionArguments}";
        }

        $functionUses = array_filter(explode(',', trim($functionUses ?? '', '()')), 'strlen');

        // Add `$__env` and `$__bladeCompiler` to allow usage of other Blade directives inside the scoped slot
        array_push($functionUses, '$__env');
        array_push($functionUses, '$__bladeCompiler');

        $functionUses = implode(',', $functionUses);

        return "<?php \$__bladeCompiler = \$__bladeCompiler ?? null; \$__env->slot({$name}, {$functionArguments} use ({$functionUses}) { ?>";
    }

    public static function endscope()
    {
        return '<?php }); ?>';
    }
}

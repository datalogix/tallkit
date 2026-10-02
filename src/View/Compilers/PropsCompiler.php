<?php

namespace TALLKit\View\Compilers;

use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

// Fixes two Blade bugs of forwarded props: a bound value escaped twice, a kebab-case one never reaching its camelCase.
class PropsCompiler
{
    public static function compile(string $value): string
    {
        $offset = 0;

        while (\preg_match('/(?<![\w@])@props[ \t]*\(/', $value, $match, PREG_OFFSET_CAPTURE, $offset)) {
            [$directive, $start] = $match[0];
            $open = $start + \strlen($directive) - 1;
            $end = static::closingParenthesis($value, $open);

            if ($end === null) {
                break;
            }

            $props = \substr($value, $open + 1, $end - $open - 1);
            // Evaluated once: Blade's @props evaluates its expression twice more.
            $compiled = "<?php \$__tkProps = {$props}; foreach (\\TALLKit\\View\\Compilers\\PropsCompiler::unescape(get_defined_vars(), \$__tkProps) as \$__tkName => \$__tkValue) { \$\$__tkName = \$__tkValue; } unset(\$__tkName, \$__tkValue); ?>\n@props(\$__tkProps)\n<?php unset(\$__tkProps); ?>";
            $value = \substr_replace($value, $compiled, $start, $end + 1 - $start);
            $offset = $start + \strlen($compiled);
        }

        return $value;
    }

    public static function unescape(array $vars, array $props): array
    {
        $attributes = $vars['attributes'] ?? null;

        if (! $attributes instanceof ComponentAttributeBag) {
            return [];
        }

        $propNames = ComponentAttributeBag::extractPropNames($props);
        $declared = [];

        foreach ($props as $key => $value) {
            $declared[\is_string($key) ? $key : $value] = true;
        }

        $values = [];
        $unescaped = [];
        $changed = false;

        foreach ($attributes->getAttributes() as $key => $value) {
            if (! \is_string($key) || ! \in_array($key, $propNames, true)) {
                $values[$key] = $value;

                continue;
            }

            $decoded = \is_string($value) ? \htmlspecialchars_decode($value, ENT_QUOTES) : $value;
            $camel = Str::camel($key);
            $name = $camel !== $key && isset($declared[$camel]) && ! $attributes->has($camel) ? $camel : $key;

            if ($decoded !== $value || $name !== $key) {
                $changed = true;

                if (\is_string($value) && ($vars[$key] ?? null) === $value) {
                    $unescaped[$key] = $decoded;
                }
            }

            $values[$name] = $decoded;
        }

        return $changed ? ['attributes' => new ComponentAttributeBag($values)] + $unescaped : [];
    }

    protected static function closingParenthesis(string $value, int $open): ?int
    {
        $depth = 0;
        $quote = null;

        for ($i = $open, $length = \strlen($value); $i < $length; $i++) {
            $char = $value[$i];

            if ($quote !== null) {
                if ($char === '\\') {
                    $i++;
                } elseif ($char === $quote) {
                    $quote = null;
                }

                continue;
            }

            if ($char === '/' && ($value[$i + 1] ?? '') === '/' || $char === '#') {
                $i = ($newline = \strpos($value, "\n", $i)) === false ? $length : $newline;

                continue;
            }

            if ($char === '/' && ($value[$i + 1] ?? '') === '*') {
                $i = ($close = \strpos($value, '*/', $i + 2)) === false ? $length : $close + 1;

                continue;
            }

            if ($char === '"' || $char === "'") {
                $quote = $char;
            } elseif ($char === '(') {
                $depth++;
            } elseif ($char === ')' && --$depth === 0) {
                return $i;
            }
        }

        return null;
    }
}

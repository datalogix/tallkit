<?php

namespace TALLKit\View\Compilers;

use Illuminate\Support\Str;
use Illuminate\View\Compilers\ComponentTagCompiler as BaseComponentTagCompiler;

class ComponentTagCompiler extends BaseComponentTagCompiler
{
    // disabled="false" still disables in HTML. Not "open": dropdown and tooltip:open take a mode.
    protected const BOOLEAN_ATTRIBUTES = ['autofocus', 'checked', 'disabled', 'hidden', 'inert', 'multiple', 'readonly', 'required'];

    protected function getAttributesFromAttributeString(string $attributeString)
    {
        $attributes = parent::getAttributesFromAttributeString($attributeString);

        foreach ($attributes as $name => $value) {
            // x-bind:disabled and ::disabled are Alpine's: a JS expression.
            if (Str::startsWith($name, ['x-', ':']) || ! in_array(Str::afterLast($name, ':'), self::BOOLEAN_ATTRIBUTES, true)) {
                continue;
            }

            // disabled="{{ $off }}" is an expression too: false would arrive as "", which disables.
            if (isset($this->boundAttributes[$name]) || ! preg_match("/^'(?:[^'\\\\]|\\\\.)*'$/s", $value)) {
                $attributes[$name] = '\\TALLKit\\Facades\\TALLKit::isAttributeEnabled('.$value.')';
                $this->boundAttributes[$name] = true;
            } elseif (in_array($value, ["'false'", "'0'"], true)) {
                $attributes[$name] = 'false';
                $this->boundAttributes[$name] = true;
            }
        }

        return $attributes;
    }

    // Laravel's own attribute pattern for `<x-...>` tags.
    protected function attributesPattern(): string
    {
        return "
            (?:
                \s+
                (?:
                    (?:
                        @(?:class)(\( (?: (?>[^()]+) | (?-1) )* \))
                    )
                    |
                    (?:
                        @(?:style)(\( (?: (?>[^()]+) | (?-1) )* \))
                    )
                    |
                    (?:
                        \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                    )
                    |
                    (?:
                        (\:\\\$)(\w+)
                    )
                    |
                    (?:
                        [\w\-:.@%]+
                        (
                            =
                            (?:
                                \\\"[^\\\"]*\\\"
                                |
                                \'[^\']*\'
                                |
                                [^\'\\\"=<>]+
                            )
                        )?
                    )
                )
            )*
        ";
    }

    protected function compileOpeningTags(string $value)
    {
        $pattern = "/
            <
                \s*
                tk\:([\w\-\:\.]*)
                (?<attributes>{$this->attributesPattern()}
                    \s*
                )
                (?<![\/=\-])
            >
        /x";

        return preg_replace_callback($pattern, function (array $matches) {
            $this->boundAttributes = [];

            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);

            return $this->componentString('tallkit::'.$matches[1], $attributes);
        }, $value);
    }

    protected function compileSelfClosingTags(string $value)
    {
        $pattern = "/
            <
                \s*
                tk\:([\w\-\:\.]*)
                \s*
                (?<attributes>{$this->attributesPattern()}
                    \s*
                )
            \/>
        /x";

        return preg_replace_callback($pattern, function (array $matches) {
            $this->boundAttributes = [];

            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);

            if (isset($attributes['slot'])) {
                $slot = $attributes['slot'];

                unset($attributes['slot']);

                return '@slot('.$slot.') '.$this->componentString('tallkit::'.$matches[1], $attributes)."\n@endComponentClass##END-COMPONENT-CLASS##".' @endslot';
            }

            return $this->componentString('tallkit::'.$matches[1], $attributes)."\n@endComponentClass##END-COMPONENT-CLASS##";
        }, $value);
    }

    protected function compileClosingTags(string $value)
    {
        return preg_replace("/<\/\s*tk\:[\w\-\:\.]*\s*>/", ' @endComponentClass##END-COMPONENT-CLASS##', $value);
    }
}

<?php

namespace TALLKit\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\ComponentAttributeBag;

trait InteractsWithTooltip
{
    public function tooltip(mixed $tooltip, ComponentAttributeBag $attributes, ?string $name = null, bool $isName = false): array
    {
        [$text, $html] = $this->tooltipContent($tooltip);

        if ($text === null) {
            return ['attributes' => [], 'describedBy' => null, 'html' => null];
        }

        $options = $attributes->prefixed('tooltip:');
        $mode = $options->get('open');
        $kbd = $options->get('kbd');
        $delay = $options->get('delay');
        $describes = ! $isName && $mode !== 'manual' && $this->tooltipNormalize($text) !== $this->tooltipNormalize((string) $name);
        $id = $this->generateId('tip', $this->shortHash($text."\n".$kbd, 10));
        $arrow = $options->get('arrow');
        $noArrow = $options->get('no-arrow');

        if ($this->isAttributeEnabled($noArrow)) {
            $arrow = false;
        }

        return [
            'attributes' => array_filter([
                $this->dataKey('tooltip') => $text,
                $this->dataKey('tooltip-id') => $describes ? $id : null,
                $this->dataKey('tooltip-position') => $options->get('position'),
                $this->dataKey('tooltip-align') => $options->get('align'),
                $this->dataKey('tooltip-delay') => $this->tooltipDelay($delay),
                $this->dataKey('tooltip-variant') => $options->get('variant'),
                $this->dataKey('tooltip-color') => $options->get('color'),
                $this->dataKey('tooltip-size') => $options->get('size'),
                $this->dataKey('tooltip-arrow') => $arrow === null ? null : ($this->isAttributeEnabled($arrow) ? 'true' : 'false'),
                $this->dataKey('tooltip-kbd') => is_string($kbd) && $kbd !== '' ? $kbd : null,
                $this->dataKey('tooltip-open') => $mode,
                $this->dataKey('tooltip-class') => $options->get('class'),
            ], fn ($value) => $value !== null && $value !== false),
            'describedBy' => $describes ? $id : null,
            'html' => $html,
        ];
    }

    // A bare number is milliseconds; '200ms' or '0.2s' go as they are (the script reads the unit).
    protected function tooltipDelay(mixed $delay): int|string|null
    {
        if (is_numeric($delay)) {
            return (int) $delay;
        }

        return is_string($delay) && preg_match('/^\d*\.?\d+m?s$/', $delay = trim($delay)) ? $delay : null;
    }

    protected function tooltipContent(mixed $tooltip): array
    {
        if ($tooltip === null || $tooltip === false || $tooltip === true || $tooltip === '') {
            return [null, null];
        }

        if ($tooltip instanceof Htmlable) {
            $html = trim($tooltip->toHtml());
        } elseif (is_string($tooltip) || is_numeric($tooltip)) {
            $translated = __((string) $tooltip);
            $html = is_string($translated) && $translated !== (string) $tooltip ? $translated : null;

            if ($html === null) {
                return [(string) $tooltip, null];
            }
        } else {
            return [null, null];
        }

        $text = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5));

        if ($text === '') {
            return [null, null];
        }

        return [$text, $html !== strip_tags($html) ? $html : null];
    }

    protected function tooltipNormalize(string $text): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5))));
    }

    public function withTooltip(ComponentAttributeBag $attributes, array $tooltip, bool $describe = true): ComponentAttributeBag
    {
        $attributes = $attributes->whereDoesntStartWith(['tooltip:'])->merge($tooltip['attributes']);

        return $describe ? $this->withDescribedBy($attributes, $tooltip['describedBy']) : $attributes;
    }

    public function withDescribedBy(ComponentAttributeBag $attributes, ?string $id): ComponentAttributeBag
    {
        if (! $id) {
            return $attributes;
        }

        $ids = collect(explode(' ', (string) $attributes->get('aria-describedby')))->filter()->push($id)->unique()->implode(' ');

        return $attributes->except('aria-describedby')->merge(['aria-describedby' => $ids]);
    }
}

<?php

namespace TALLKit\Concerns;

use Illuminate\View\ComponentAttributeBag;

trait InteractsWithAttributes
{
    public function isAttributeEnabled(mixed $value): bool
    {
        return ! in_array($value, [null, false, '', '0', 0, 'false'], true);
    }

    public function attributesMerge(array|ComponentAttributeBag|null ...$sets): ComponentAttributeBag
    {
        $attributes = null;

        foreach ($sets as $set) {
            if ($set === null) {
                continue;
            }

            $values = array_filter(
                $set instanceof ComponentAttributeBag ? $set->getAttributes() : $set,
                static fn (mixed $value): bool => $value !== null,
            );

            $attributes = $attributes === null
                ? new ComponentAttributeBag($values)
                : (new ComponentAttributeBag($values))->merge($attributes->getAttributes(), escape: false);
        }

        return $attributes ?? new ComponentAttributeBag;
    }

    public function attributesFromItem(mixed $item, string $key = 'label'): array
    {
        if ($item instanceof ComponentAttributeBag) {
            return $item->getAttributes();
        }

        return array_map(
            static fn (mixed $value): mixed => \is_string($value) ? e($value) : $value,
            \is_array($item) ? $item : [$key => $item],
        );
    }
}

<?php

namespace TALLKit\Components\Avatar;

use TALLKit\View\BladeComponent;

class Avatar extends BladeComponent
{
    protected function props()
    {
        return [
            'size' => null,
            'alt' => null,
            'src' => null,
            'initials' => null,
            'circle' => null,
            'color' => null,
            'icon' => null,
            'name' => null,
            'description' => null,
        ];
    }

    protected function mounted(array $data)
    {
        $this->initials = $this->generateInitials($this->initials ?? $this->name);

        if ($this->color === 'auto') {
            $this->color = $this->generateColor();
        }
    }

    protected function generateInitials($name)
    {
        $parts = str($name)->title()->ucsplit()->filter();

        if ($parts->isEmpty()) {
            return null;
        }

        if ($this->attributes->pluck('initials:single')) {
            return strtoupper($parts[0][0]);
        }

        if ($parts->count() > 1) {
            return strtoupper($parts[0][0].$parts[1][0]);
        }

        return strtoupper($parts[0][0]).strtolower($parts[0][1]);
    }

    protected function generateColor()
    {
        $colors = ['red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'];
        $colorSeed = $this->attributes->pluck('color:seed') ?? $this->name ?? $this->icon ?? $this->initials;
        $hash = crc32((string) $colorSeed);

        return $colors[$hash % count($colors)];
    }
}

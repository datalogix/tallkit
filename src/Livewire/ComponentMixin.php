<?php

namespace TALLKit\Livewire;

class ComponentMixin
{
    public function toast()
    {
        return function (?string $text = null, ?string $heading = null, ?string $type = null, ?int $duration = null, ?string $position = null) {
            $this->js('$tallkit.toast', $text, $heading, $type, $duration, $position);
        };
    }
}

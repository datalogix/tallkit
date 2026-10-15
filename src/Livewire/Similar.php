<?php

namespace TALLKit\Livewire;

use Attribute;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attribute as LivewireAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Similar extends LivewireAttribute
{
    public function __construct(public ?string $record = null) {}

    public function getRecord(): ?Model
    {
        $record = $this->record ? data_get($this->component, $this->record) : null;

        return $record instanceof Model ? $record : null;
    }

    public function update(): void
    {
        $this->component->reset('similar');
        $this->component->resetValidation($this->getName());
    }
}

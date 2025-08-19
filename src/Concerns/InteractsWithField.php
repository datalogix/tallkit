<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Str;
use TALLKit\Attributes\Mount;

trait InteractsWithField
{
    use AppendsCustomAttributes;
    use InteractsWithFieldValue;

    protected function customAppendedAttributes()
    {
        return ['name', 'label', 'labelAppend', 'labelPrepend', 'id', 'description', 'help', 'badge', 'information', 'size', 'showError', 'prefix', 'suffix'];
    }

    #[Mount(20)]
    protected function mountField(array $data)
    {
        $this->manageProp($data, 'id');
        $this->manageProp($data, 'invalid');
        $this->manageProp($data, 'placeholder');

        $wireModel = $this->attributes->whereStartsWith('wire:model')->first();
        $xModel = $this->attributes->whereStartsWith('x-model')->first();

        $this->name ??= $wireModel ?? $xModel;
        $fieldName = $this->getFieldName();
        $this->id ??= uniqid($fieldName);
        $this->label = $this->label === true || $this->label === null ? Str::headline(Str::before($fieldName, '_id')) : $this->label;
        $this->placeholder = $this->placeholder === true ? $this->label : $this->placeholder;

        if (! $wireModel && in_livewire() && $fieldName && ! $xModel) {
            $this->attributes = $this->attributes->merge(['wire:model' => $fieldName]);
        }
    }
}

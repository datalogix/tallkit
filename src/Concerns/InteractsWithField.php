<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Str;
use TALLKit\Attributes\Mount;

trait InteractsWithField
{
    use AppendsCustomAttributes;
    use InteractsWithErrorBag;
    use InteractsWithFieldValue;

    protected function customAppendedAttributes()
    {
        return [
            'name', 'id', 'size',
            'label', 'labelAppend', 'labelPrepend',
            'description', 'help', 'badge', 'info',
            'showError',
            'prefix', 'suffix',
            'prepend', 'append', 'icon', 'iconTrailing', 'kbd', 'loading',
        ];
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
        // $this->id ??= $fieldName ?? uniqid();
        $this->label = $this->label === true || $this->label === null ? Str::headline(Str::before($fieldName, '_id')) : $this->label;
        $this->placeholder = $this->placeholder === true ? $this->label : $this->placeholder;
        $this->invalid ??= $this->name && $this->hasError($this->name);

        if (! $wireModel && in_livewire() && $fieldName && ! $xModel) {
            $this->attributes = $this->attributes->merge(['wire:model' => $fieldName]);
        }
    }
}

<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Str;
use TALLKit\Attributes\Mount;

trait InteractsWithFieldValue
{
    use BoundValues;

    #[Mount(30)]
    protected function mountFieldValue(array $data)
    {
        $this->manageProp($data, 'bind');
        $this->manageProp($data, 'value');
        $this->manageProp($data, 'default');
        $this->manageProp($data, 'language');

        $this->value ??= $this->getValue($data['slot']->toHtml() ?: $this->default);
    }

    protected $fieldKey;

    protected $fieldName;

    public function getFieldKey()
    {
        return $this->fieldKey ??= Str::before($this->name, '[]');
    }

    public function getFieldName()
    {
        return $this->fieldName ??= Str::replace(['[', ']'], ['.', ''], $this->getFieldKey());
    }

    public function getFieldValue($default = null)
    {
        return $this->oldFieldValue($this->getFieldBoundValue() ?? $default);
    }

    public function oldFieldValue($default = null)
    {
        return old($this->getFieldName(), $default);
    }

    public function getFieldBoundValue()
    {
        return $this->getBoundValue($this->bind, $this->getFieldName());
    }

    public function getValue($default = null)
    {
        if (empty($this->name)) {
            return $default;
        }

        if (in_livewire()) {
            return;
        }

        if (! $this->language) {
            return $this->formatValue($this->getFieldValue($default));
        }

        if ($this->bind !== false) {
            $this->bind ??= $this->getBoundTarget();
        }

        if ($this->bind) {
            $default = $this->formatValue(
                $this->bind->getTranslation($this->getFieldKey(), $this->language, false)
            ) ?: $default;
        }

        return old("{$this->getFieldName()}.{$this->language}", $default);
    }

    protected function formatValue($value)
    {
        return $value;
    }
}

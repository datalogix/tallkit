<?php

namespace TALLKit\Components\Checkbox;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use TALLKit\Attributes\Mount;
use TALLKit\Concerns\InteractsWithField;
use TALLKit\View\BladeComponent;

class Checkbox extends BladeComponent
{
    use InteractsWithField;

    public function __construct(
        public mixed $checked = null,
        public ?string $variant = null,
        public ?string $iconOn = null,
        public ?string $iconOff = null,
    ) {}

    #[Mount]
    protected function mount()
    {
        $this->checked = in_array($this->value, Arr::wrap($this->checked ?? $this->isChecked()));
    }

    public function getValue($default = null)
    {
        return $default ?? 1;
    }

    protected function isChecked()
    {
        if (! session()->hasOldInput() && ! in_livewire()) {
            $boundValue = $this->getFieldBoundValue();

            if ($boundValue instanceof Collection && $firstItem = $boundValue->first()) {
                $boundValue = $boundValue->pluck($firstItem->getKeyName())->toArray();
            }

            if ($boundValue instanceof Arrayable) {
                $boundValue = $boundValue->toArray();
            }

            if (is_array($boundValue) || $boundValue !== null) {
                return $boundValue;
            }

            return $this->default ?? false;
        }

        if ($oldData = $this->oldFieldValue()) {
            return $oldData;
        }
    }
}

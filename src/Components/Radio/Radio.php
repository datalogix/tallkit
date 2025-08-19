<?php

namespace TALLKit\Components\Radio;

use TALLKit\Attributes\Mount;
use TALLKit\Concerns\InteractsWithField;
use TALLKit\View\BladeComponent;

class Radio extends BladeComponent
{
    use InteractsWithField;

    public function __construct(
        public mixed $checked = null,
        public ?string $variant = null,
    ) {}

    #[Mount]
    protected function mount()
    {
        $this->checked ??= $this->isChecked();
    }

    protected function isChecked()
    {
        if (! session()->hasOldInput() && ! in_livewire()) {
            $boundValue = $this->getFieldBoundValue();

            if ($boundValue !== null) {
                return $boundValue == $this->value;
            }

            return $this->default ?? false;
        }

        if ($oldData = $this->oldFieldValue()) {
            return $oldData == $this->value;
        }
    }
}

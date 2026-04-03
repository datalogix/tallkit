<?php

namespace TALLKit\Components\Field;

use Illuminate\View\ComponentSlot;
use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Control extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
        public null|bool|string|ComponentSlot $prepend = null,
        public null|bool|string|ComponentSlot $append = null,
        public string|ComponentSlot|null $icon = null,
        public string|ComponentSlot|null $iconTrailing = null,
        public string|ComponentSlot|null $kbd = null,
        public bool|string|null $loading = null,
    ) {}

    #[Mount]
    protected function mountLoading(array $data)
    {
        $this->setVariables('wireTarget');
        $this->wireTarget = null;

        if (! (is_string($this->loading) || $this->loading === true)) {
            return;
        }

        $wireModel = $this->attributes->wire('model');

        if ($wireModel?->directive && $wireModel->hasModifier('live')) {
            $this->loading = true;
            $this->wireTarget = $wireModel->value();

            return;
        }

        $this->wireTarget = $this->loading;
        $this->loading = (bool) $this->loading;
    }
}

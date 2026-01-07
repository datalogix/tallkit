<?php

namespace TALLKit\Components\Textarea;

use TALLKit\Attributes\Mount;
use TALLKit\Concerns\InteractsWithField;
use TALLKit\View\BladeComponent;

class Textarea extends BladeComponent
{
    use InteractsWithField;

    public function __construct(
        public ?string $variant = null,
        public ?string $resize = null,
        public null|string|int $rows = null,
        public ?int $maxRows = null,
    ) {}

    #[Mount()]
    protected function mountLoading(array $data)
    {
        $this->manageProp($data, 'loading');
        $this->manageProp($data, 'wireTarget');

        if (! (is_string($this->loading) || $this->loading === null)) {
            return;
        }

        $wireModel = $this->attributes->wire('model');

        if ($wireModel?->directive) {
            $this->loading = $wireModel->hasModifier('live');
            $this->wireTarget = $this->loading ? $wireModel->value() : null;

            return;
        }

        $this->wireTarget = $this->loading;
        $this->loading = (bool) $this->loading;
    }
}

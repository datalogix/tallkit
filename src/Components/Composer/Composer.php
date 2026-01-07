<?php

namespace TALLKit\Components\Composer;

use TALLKit\Attributes\Mount;
use TALLKit\Concerns\InteractsWithField;
use TALLKit\View\BladeComponent;

class Composer extends BladeComponent
{
    use InteractsWithField;

    public function __construct(
        public ?int $rows = null,
        public ?int $maxRows = null,
        public ?bool $inline = null,
        public ?string $submit = null,
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

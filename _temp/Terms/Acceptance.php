<?php

namespace TALLKit\Components\Terms;

use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Acceptance extends BladeComponent
{
    public function __construct(
        public ?bool $checkbox = null,
        public ?string $termsOfService = null,
        public ?string $termsOfUse = null,
        public ?string $privacyPolicy = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->termsOfService ??= route_detect(['terms-of-service', 'terms.terms-of-service'], default: null);
        $this->termsOfUse ??= route_detect(['terms-of-use', 'terms.terms-of-use'], default: null);
        $this->privacyPolicy ??= route_detect(['privacy-policy', 'terms.privacy-policy'], default: null);
    }
}

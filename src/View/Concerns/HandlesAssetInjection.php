<?php

namespace TALLKit\View\Concerns;

use TALLKit\Assets\AssetInjector;
use TALLKit\Attributes\Finish;

trait HandlesAssetInjection
{
    #[Finish()]
    protected function finishAssets()
    {
        AssetInjector::markComponentAsRendered();
    }
}

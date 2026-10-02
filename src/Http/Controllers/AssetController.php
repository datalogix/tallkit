<?php

namespace TALLKit\Http\Controllers;

use Illuminate\Routing\Controller;
use TALLKit\Assets\CanPretendToBeAFile;

class AssetController extends Controller
{
    use CanPretendToBeAFile;

    // A controller, not a closure: `route:cache` breaks closures that hold objects.
    public function script()
    {
        return $this->pretendResponseIsFile(
            config('app.debug')
                ? __DIR__.'/../../../dist/tallkit.js'
                : __DIR__.'/../../../dist/tallkit.min.js'
        );
    }
}

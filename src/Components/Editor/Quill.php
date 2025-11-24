<?php

namespace TALLKit\Components\Editor;

use TALLKit\Concerns\InteractsWithField;
use TALLKit\Concerns\InteractsWithJsonOptions;
use TALLKit\View\BladeComponent;

class Quill extends BladeComponent
{
    use InteractsWithField;
    use InteractsWithJsonOptions;
}

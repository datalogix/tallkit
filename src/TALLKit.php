<?php

namespace TALLKit;

use Illuminate\Support\Facades\Session;
use Illuminate\View\ComponentSlot;
use TALLKit\Assets\AssetManager;
use TALLKit\Concerns\InteractsWithAlert;
use TALLKit\Concerns\InteractsWithAttributes;
use TALLKit\Concerns\InteractsWithAvatar;
use TALLKit\Concerns\InteractsWithColor;
use TALLKit\Concerns\InteractsWithElement;
use TALLKit\Concerns\InteractsWithErrorBags;
use TALLKit\Concerns\InteractsWithField;
use TALLKit\Concerns\InteractsWithIcon;
use TALLKit\Concerns\InteractsWithLivewire;
use TALLKit\Concerns\InteractsWithLocale;
use TALLKit\Concerns\InteractsWithModal;
use TALLKit\Concerns\InteractsWithNaming;
use TALLKit\Concerns\InteractsWithOptions;
use TALLKit\Concerns\InteractsWithSize;
use TALLKit\Concerns\InteractsWithStepper;
use TALLKit\Concerns\InteractsWithTable;
use TALLKit\Concerns\InteractsWithToast;
use TALLKit\Concerns\InteractsWithTooltip;
use TALLKit\Concerns\InteractsWithUpload;
use TALLKit\Concerns\InteractsWithUrl;
use TALLKit\Concerns\InteractsWithUser;
use TALLKit\View\ClassBuilder;

class TALLKit
{
    public const PREFIX = 'tallkit';

    use InteractsWithAlert;
    use InteractsWithAttributes;
    use InteractsWithAvatar;
    use InteractsWithColor;
    use InteractsWithElement;
    use InteractsWithErrorBags;
    use InteractsWithField;
    use InteractsWithIcon;
    use InteractsWithLivewire;
    use InteractsWithLocale;
    use InteractsWithModal;
    use InteractsWithNaming;
    use InteractsWithOptions;
    use InteractsWithSize;
    use InteractsWithStepper;
    use InteractsWithTable;
    use InteractsWithToast;
    use InteractsWithTooltip;
    use InteractsWithUpload;
    use InteractsWithUrl;
    use InteractsWithUser;

    public function scripts(?array $options = null)
    {
        return AssetManager::scripts($options);
    }

    public function classes(...$classes)
    {
        return new ClassBuilder($classes);
    }

    protected function sessionStarted(): bool
    {
        return app()->bound('session') && Session::isStarted();
    }

    public function isSlot($slot)
    {
        return $slot instanceof ComponentSlot;
    }
}

<?php

namespace TALLKit\Livewire\Mixins;

use TALLKit\Facades\TALLKit;

class UploadMixin
{
    public function uploadPrune()
    {
        return fn ($original, $current, ?string $disk = null) => TALLKit::uploadPrune($original, $current, $disk);
    }

    public function uploadKept()
    {
        return fn ($original, $current, ?string $disk = null) => TALLKit::uploadKept($original, $current, $disk);
    }
}

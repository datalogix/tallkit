<?php

namespace TALLKit\Components\Upload;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use TALLKit\Concerns\InteractsWithField;
use TALLKit\View\BladeComponent;

class Upload extends BladeComponent
{
    use InteractsWithField;

    public function __construct(
        public ?bool $multiple = null,
        public ?bool $droppable = null,
        public $value = null,
    ) {}

    public function getFiles($value)
    {
        return Collection::wrap($value)->map(function ($file) {
            if ($file instanceof TemporaryUploadedFile) {
                $extension = $file->getClientOriginalExtension();

                return (object) [
                    'path' => $file->getRealPath(),
                    'url' => $file->temporaryUrl(),
                    'extension' => $extension,
                    'type' => $this->getTypeFromExtension($extension),
                ];
            } elseif (is_string($file)) {
                $file = Str::replaceFirst(Storage::url(''), '', $file);

                if (! Storage::exists($file)) {
                    return;
                }

                $extension = File::extension($file);

                return (object) [
                    'path' => Storage::path($file),
                    'url' => Storage::url($file),
                    'extension' => $extension,
                    'type' => $this->getTypeFromExtension($extension),
                ];
            }
        })->filter();
    }

    protected function getTypeFromExtension(string $extension)
    {
        return match ($extension) {
            'jpg', 'jpeg', 'png', 'gif' => 'image',
            'mp4' => 'video',
            'mp3' => 'audio',
            'pdf' => 'pdf',
            'doc', 'docx' => 'doc',
            'xls', 'xlsx' => 'xls',
            'ppt', 'pptx' => 'ppt',
            'rar', 'zip', '7z' => 'archive',
            'txt', 'md' => 'text',
            'csv' => 'csv',
            'json', 'js', 'ts', 'html', 'css' => 'code',
            default => 'unknown',
        };
    }
}

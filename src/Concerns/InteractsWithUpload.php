<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\FileNotPreviewableException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait InteractsWithUpload
{
    public function getUploadedFiles($value)
    {
        return Collection::wrap($value)->map(function ($file) {
            if (class_exists(TemporaryUploadedFile::class)
                && $file instanceof TemporaryUploadedFile) {
                return [
                    'name' => $file->getClientOriginalName(),
                    'url' => $this->temporaryUploadUrl($file),
                    'size' => $file->getSize(),
                    'type' => $this->getUploadFileType($file->getClientOriginalExtension()),
                    'status' => 'done',
                    'progress' => 100,
                    'value' => null,
                    'tmpFilename' => $file->getFilename(),
                ];
            }

            if (is_string($file)) {
                $path = Str::replaceFirst(Storage::url(''), '', $file);

                if (! Storage::exists($path)) {
                    return null;
                }

                return [
                    'name' => File::basename($path),
                    'url' => Storage::url($path),
                    'size' => Storage::size($path),
                    'type' => $this->getUploadFileType(File::extension($path)),
                    'status' => 'done',
                    'progress' => 100,
                    'value' => $file,
                    'tmpFilename' => null,
                ];
            }

            return null;
        })->filter()->values();
    }

    protected function temporaryUploadUrl(TemporaryUploadedFile $file): ?string
    {
        try {
            return $file->temporaryUrl();
        } catch (FileNotPreviewableException) {
            return null;
        }
    }

    public function getUploadFileType(?string $extension)
    {
        return match (Str::lower((string) $extension)) {
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

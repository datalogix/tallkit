<?php

namespace TALLKit\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use TALLKit\Facades\TALLKit;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $type = TALLKit::getUploadFileType($request->file('file')?->getClientOriginalExtension());
        $maxSize = $this->resolveMaxSize($type, $request->integer('max_size'));

        $request->validate([
            'file' => ['required', 'file', "max:{$maxSize}", 'mimes:'.implode(',', $this->allowedExtensions())],
            'disk' => ['nullable', 'string'],
            'directory' => ['nullable', 'string'],
        ]);

        $disk = $this->resolveDisk($request->input('disk'));
        $directory = $this->resolveDirectory($request->input('directory'));

        $path = $request->file('file')->store($directory, $disk);

        return response()->json([
            'url' => Storage::disk($disk)->url($path),
        ]);
    }

    protected function resolveMaxSize(string $type, int $requested): int
    {
        $sizes = config('tallkit.upload.max_size', 20480);
        $ceiling = is_array($sizes) ? (int) ($sizes[$type] ?? $sizes['default'] ?? 20480) : (int) $sizes;

        return $requested > 0 ? min($requested, $ceiling) : $ceiling;
    }

    protected function allowedExtensions(): array
    {
        return config('tallkit.upload.allowed_extensions', [
            'jpg', 'jpeg', 'png', 'gif', 'webp',
            'mp4', 'mov', 'webm',
            'mp3', 'wav',
            'pdf',
            'doc', 'docx',
            'xls', 'xlsx',
            'ppt', 'pptx',
            'zip', 'rar', '7z',
            'txt', 'md', 'csv',
        ]);
    }

    protected function resolveDisk(?string $disk): string
    {
        return filled($disk) ? $disk : config('tallkit.upload.disk', 'public');
    }

    protected function resolveDirectory(?string $directory): string
    {
        if (blank($directory)) {
            return config('tallkit.upload.directory', 'tallkit-uploads');
        }

        $segments = array_filter(
            explode('/', str_replace('\\', '/', $directory)),
            fn ($segment) => $segment !== '' && $segment !== '.' && $segment !== '..',
        );

        return $segments !== [] ? implode('/', $segments) : config('tallkit.upload.directory', 'tallkit-uploads');
    }
}

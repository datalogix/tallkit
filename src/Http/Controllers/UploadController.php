<?php

namespace TALLKit\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use TALLKit\Facades\TALLKit;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        if (! $request->hasValidRelativeSignature()) {
            abort(403, __('The upload link is invalid or has expired. Reload the page and try again.'));
        }

        if (! $this->mayUpload($request)) {
            abort(401, __('Unauthenticated.'));
        }

        if (is_array($request->file('file'))) {
            $attribute = trans()->has('validation.attributes.file') ? __('validation.attributes.file') : 'file';

            throw ValidationException::withMessages(['file' => __('validation.file', ['attribute' => $attribute])]);
        }

        $params = $request->query();
        $types = array_filter(explode(',', (string) ($params['types'] ?? '')));
        $type = TALLKit::uploadedFileType($request->file('file'));

        $signedMax = $params['max_size'] ?? null;
        $signedMax = (int) (is_array($signedMax) ? ($signedMax[$type] ?? 0) : $signedMax);
        // The body may only ask for a smaller size: the query's is the signed one.
        $asked = $request->request->get('max_size');
        $limits = array_filter([$signedMax, is_numeric($asked) ? (int) $asked : 0]);
        $maxSize = TALLKit::uploadMaxSize($type, $limits ? min($limits) : null);

        $extensions = $types
            ? collect($types)->flatMap(fn ($kind) => TALLKit::uploadAllowedExtensions($kind))->unique()->all()
            : TALLKit::uploadAllowedExtensions();

        $request->validate([
            'file' => ['required', 'file', "max:{$maxSize}", 'mimes:'.implode(',', $extensions ?: ['none'])],
        ]);

        $disk = TALLKit::uploadDisk($params['disk'] ?? null);

        if (! TALLKit::uploadDiskAllowed($disk)) {
            throw ValidationException::withMessages([
                'disk' => __('The selected disk is invalid.'),
            ]);
        }

        $directory = $this->resolveDirectory($params['directory'] ?? null);

        // store() may return false or throw: fail explicitly, or the editor inserts a broken image.
        try {
            $path = $request->file('file')->store($directory, $disk);
        } catch (\Throwable $e) {
            report($e);

            abort(500, __('The file could not be saved.'));
        }

        if ($path === false) {
            report(new \RuntimeException("TALLKit: an upload could not be saved on the [{$disk}] disk, in [{$directory}]."));

            abort(500, __('The file could not be saved.'));
        }

        try {
            $url = Storage::disk($disk)->url($path);
        } catch (\Throwable) {
            $url = null;
        }

        if (blank($url)) {
            Storage::disk($disk)->delete($path);

            abort(422, __('The file was uploaded to a disk with no public address.'));
        }

        return response()->json([
            'url' => $url,
            'path' => $path,
            'disk' => $disk,
        ]);
    }

    protected function mayUpload(Request $request): bool
    {
        return auth(config('tallkit.upload.guard'))->check() || (bool) $request->query('guest');
    }

    // Always inside the configured directory.
    protected function resolveDirectory(?string $directory): string
    {
        $base = TALLKit::uploadDirectory();

        $segments = array_filter(
            explode('/', str_replace('\\', '/', (string) $directory)),
            fn ($segment) => $segment !== '' && $segment !== '.' && $segment !== '..',
        );

        return implode('/', array_filter([$base, ...$segments], fn ($segment) => $segment !== ''));
    }
}

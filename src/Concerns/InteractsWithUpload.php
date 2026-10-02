<?php

namespace TALLKit\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\Mime\MimeTypes;
use TALLKit\Rules\Upload;

trait InteractsWithUpload
{
    protected $uploadsInUse = null;

    public function uploadsInUse(callable $callback): void
    {
        $this->uploadsInUse = $callback;
    }

    public function hasUploadsInUse(): bool
    {
        return $this->uploadsInUse !== null;
    }

    public function uploadsInUseOf(array $paths, string $disk): array
    {
        return collect(($this->uploadsInUse)($paths, $disk))->values()->all();
    }

    public function uploadDisk(?string $disk = null): string
    {
        return filled($disk) ? $disk : (string) config('tallkit.upload.disk', 'public');
    }

    public function uploadDirectory(): string
    {
        return trim((string) config('tallkit.upload.directory', 'tallkit-uploads'), '/');
    }

    public function editorUploadPaths(?string $html, ?string $disk = null): Collection
    {
        $disk = $this->uploadDisk($disk);
        $directory = $this->uploadDirectory().'/';

        try {
            $basePath = (string) parse_url(Storage::disk($disk)->url(''), PHP_URL_PATH);
        } catch (\Throwable) {
            $basePath = '';
        }

        preg_match_all('/\b(?:src|href|poster)\s*=\s*(["\'])(.*?)\1/i', (string) $html, $matches);

        return collect($matches[2])
            ->map(fn ($url) => html_entity_decode($url, ENT_QUOTES | ENT_HTML5))
            ->map(fn ($url) => $this->uploadPath($url, $disk))
            ->map(fn ($path) => ltrim($path, '/'))
            ->map(fn ($path) => ($base = ltrim($basePath, '/')) !== '' && str_starts_with($path, $base) ? substr($path, strlen($base)) : $path)
            ->map(fn ($path) => ltrim($path, '/'))
            ->filter(fn ($path) => str_starts_with($path, $directory))
            ->unique()
            ->values();
    }

    protected function uploadUrl(
        ?string $disk = null,
        ?string $directory = null,
        bool $guest = false,
        ?array $types = null,
        array|int|null $maxSize = null,
    ): ?string {
        if (! Route::has('tallkit.upload')) {
            return null;
        }

        if (filled($disk) && ! $this->uploadDiskAllowed($disk)) {
            throw new \InvalidArgumentException("The disk [{$disk}] isn't configured, or isn't in tallkit.upload.allowed_disks.");
        }

        // Signed without the app's subfolder (as Laravel verifies it), then prefixed with it.
        return rtrim(request()->getBaseUrl(), '/').URL::temporarySignedRoute(
            'tallkit.upload',
            now()->addMinutes((int) ($guest
                ? config('tallkit.upload.guest_url_ttl', 60 * 2)
                : config('tallkit.upload.url_ttl', 60 * 24))),
            array_filter([
                'disk' => $disk,
                'directory' => $directory,
                'guest' => $guest ? 1 : null,
                'types' => $types ? implode(',', $this->uploadTypesChecked($types)) : null,
                'max_size' => is_array($maxSize)
                    ? (array_filter(array_map('intval', $maxSize)) ?: null)
                    : ($maxSize ? (int) $maxSize : null),
            ], fn ($value) => $value !== null && $value !== ''),
            absolute: false,
        );
    }

    public function editorUpload(array|bool|null $upload = null): ?array
    {
        if ($upload === false) {
            return null;
        }

        $upload = is_array($upload) ? $upload : [];

        // Only what an editor puts in its content: a guest's link isn't a place to store any file.
        $guest = (bool) ($upload['guest'] ?? false);
        $types = $upload['types'] ?? ['image', 'video'];

        if ($guest && ($guestTypes = config('tallkit.upload.guest_types')) !== null) {
            $types = array_values(array_intersect($types, (array) $guestTypes));
        }

        // A guest link with no kind left would take every kind: none at all.
        if ($types === []) {
            return null;
        }

        $upload['maxSize'] = collect($types)
            ->mapWithKeys(fn ($type) => [$type => $this->uploadMaxSize($type, collect([
                is_array($upload['maxSize'] ?? null) ? ($upload['maxSize'][$type] ?? null) : ($upload['maxSize'] ?? null),
                $guest ? config('tallkit.upload.guest_max_size') : null,
            ])->filter()->min())])
            ->all();

        $upload['url'] ??= $this->uploadUrl(
            disk: $upload['disk'] ?? null,
            directory: $upload['directory'] ?? null,
            guest: $guest,
            types: $types,
            maxSize: $upload['maxSize'],
        );

        return filled($upload['url'] ?? null) ? Arr::only($upload, ['url', 'maxSize']) : null;
    }

    protected function uploadTypesChecked(array $types): array
    {
        foreach ($types as $type) {
            if (! array_key_exists($type, $this->uploadFileTypes())) {
                throw new \InvalidArgumentException("The upload type [{$type}] doesn't exist. Use one of: ".implode(', ', array_keys($this->uploadFileTypes())).'.');
            }
        }

        return array_values($types);
    }

    public function editorUploadMessages(): array
    {
        return [
            'tooLarge' => __('The file may not be larger than :size.'),
            'invalidType' => __('This file type is not allowed.'),
            'failed' => __('The file could not be uploaded.'),
        ];
    }

    public function editorTranslations(string $editor): array
    {
        $t = fn (array $keys) => collect($keys)->mapWithKeys(fn ($text, $key) => [$key => __($text)])->all();

        return match ($editor) {
            'quill' => [
                'buttons' => $t([
                    'bold' => 'Bold', 'italic' => 'Italic', 'underline' => 'Underline', 'strike' => 'Strikethrough',
                    'header' => 'Heading', 'color' => 'Text color', 'background' => 'Highlight color', 'size' => 'Size',
                    'script:sub' => 'Subscript', 'script:super' => 'Superscript', 'align' => 'Align',
                    'indent:-1' => 'Decrease indent', 'indent:+1' => 'Increase indent', 'direction:rtl' => 'Text direction',
                    'link' => 'Link', 'list:ordered' => 'Numbered list', 'list:bullet' => 'Bullet list', 'list:check' => 'Checklist',
                    'image' => 'Image', 'video' => 'Video', 'blockquote' => 'Quote', 'code-block' => 'Code block',
                    'clean' => 'Clear formatting',
                ]),
                'texts' => $t([
                    'normal' => 'Normal', 'heading1' => 'Heading 1', 'heading2' => 'Heading 2', 'heading3' => 'Heading 3',
                    'heading4' => 'Heading 4', 'heading5' => 'Heading 5', 'heading6' => 'Heading 6',
                    'small' => 'Small', 'large' => 'Large', 'huge' => 'Huge',
                    'visit' => 'Visit URL:', 'edit' => 'Edit', 'remove' => 'Remove', 'save' => 'Save',
                    'enterLink' => 'Enter link:', 'enterVideo' => 'Enter video:', 'enterFormula' => 'Enter formula:',
                ]),
            ],
            'editorjs' => [
                'ui' => [
                    'blockTunes' => ['toggler' => $t(['Click to tune' => 'Click to tune', 'or drag to move' => 'or drag to move'])],
                    'inlineToolbar' => ['converter' => $t(['Convert to' => 'Convert to'])],
                    'toolbar' => ['toolbox' => $t(['Add' => 'Add'])],
                    'popover' => $t(['Filter' => 'Filter', 'Nothing found' => 'Nothing found', 'Convert to' => 'Convert to']),
                ],
                'toolNames' => $t([
                    'Text' => 'Text', 'Heading' => 'Heading', 'List' => 'List', 'Warning' => 'Warning', 'Checklist' => 'Checklist',
                    'Quote' => 'Quote', 'Code' => 'Code', 'Delimiter' => 'Delimiter', 'Raw HTML' => 'Raw HTML', 'Table' => 'Table',
                    'Link' => 'Link', 'Marker' => 'Marker', 'Bold' => 'Bold', 'Italic' => 'Italic', 'InlineCode' => 'Inline code',
                    'Underline' => 'Underline', 'Image' => 'Image', 'Embed' => 'Embed',
                ]),
                'tools' => [
                    'warning' => $t(['Title' => 'Title', 'Message' => 'Message']),
                    'link' => $t(['Add a link' => 'Add a link']),
                    'list' => $t(['Ordered' => 'Ordered', 'Unordered' => 'Unordered', 'Checklist' => 'Checklist']),
                ],
                'blockTunes' => [
                    'delete' => $t(['Delete' => 'Delete', 'Click to delete' => 'Click to delete']),
                    'moveUp' => $t(['Move up' => 'Move up']),
                    'moveDown' => $t(['Move down' => 'Move down']),
                ],
            ],
            default => [],
        };
    }

    // $stored: the value saved before, from the model, never from the browser (false: any path is looked up).
    public function uploadedFiles($value, ?string $disk = null, mixed $stored = false): Collection
    {
        $disk = $this->uploadDisk($disk);

        return Collection::wrap($value)->map(function ($file) use ($disk, $stored) {
            if (class_exists(TemporaryUploadedFile::class)
                && $file instanceof TemporaryUploadedFile) {
                return [
                    'name' => $file->getClientOriginalName(),
                    'url' => $this->temporaryUploadUrl($file),
                    'size' => $file->getSize(),
                    'type' => $this->uploadFileType($file->getClientOriginalExtension()),
                    'status' => 'done',
                    'progress' => 100,
                    'value' => null,
                    'tmpFilename' => $file->getFilename(),
                ];
            }

            if (is_string($file) && ($stored === false || $this->uploadIsStored($file, $stored, $disk))) {
                return $this->resolveStoredFile($file, $disk);
            }

            return null;
        })->filter()->values();
    }

    /** The browser can send any path: only the ones the server put there are looked up on the disk. */
    public function uploadServerValue(object $component, string $field, mixed $value): mixed
    {
        $strings = fn ($items) => collect($items)->flatten()->filter(fn ($item) => is_string($item) && $item !== '');

        if (! method_exists($component, 'getId') || ! request()->hasSession()) {
            return $value;
        }

        $key = $component->getId().'|'.$field;
        $shown = request()->session()->get($this->storageKey('upload-paths'), []);
        $entry = collect(request()->input('components', []))->first(fn ($entry) => is_array($entry)
            && is_string($entry['snapshot'] ?? null)
            && (json_decode($entry['snapshot'], true)['memo']['id'] ?? null) === $component->getId());

        if ($entry === null) {
            $trusted = $strings(Arr::wrap($value));
        } else {
            $before = $strings(json_decode($entry['snapshot'], true)['data'] ?? []);
            $sent = $strings(collect($entry['updates'] ?? [])
                ->filter(fn ($update, $name) => $name === $field || str_starts_with((string) $name, $field.'.')));
            $known = collect($shown[$key] ?? []);

            $trusted = $strings(Arr::wrap($value))->filter(fn ($path) => $known->contains($path)
                || (! $before->contains($path) && ! $sent->contains($path)));

            $value = is_array($value) || $value instanceof \Traversable
                ? collect($value)->reject(fn ($file) => is_string($file) && $file !== '' && ! $trusted->contains($file))->values()->all()
                : (is_string($value) && $value !== '' && ! $trusted->contains($value) ? null : $value);
        }

        unset($shown[$key]);
        $shown[$key] = $trusted->unique()->values()->all();
        request()->session()->put($this->storageKey('upload-paths'), array_slice($shown, -50, preserve_keys: true));

        return $value;
    }

    protected function resolveStoredFile(string $file, string $disk): ?array
    {
        $path = $this->uploadPath($file, $disk);
        $key = $this->uploadCacheKey($disk, $path);
        $request = app('tallkit.uploads');

        if (! isset($request[$key])) {
            $local = config("filesystems.disks.{$disk}.driver") === 'local';

            $request[$key] = $local
                ? $this->describeStoredFile($path, $disk)
                : Cache::remember($key, 60 * 5, fn () => $this->describeStoredFile($path, $disk) ?? false);
        }

        $described = $request[$key];

        return $described ? [...$described, 'value' => $file] : null;
    }

    protected function describeStoredFile(string $path, string $disk): ?array
    {
        try {
            if (! Storage::disk($disk)->exists($path)) {
                return null;
            }

            return [
                'name' => File::basename($path),
                'url' => Storage::disk($disk)->url($path),
                'size' => Storage::disk($disk)->size($path),
                'type' => $this->uploadFileType(File::extension($path)),
                'status' => 'done',
                'progress' => 100,
                'tmpFilename' => null,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    protected function uploadCacheKey(string $disk, string $path): string
    {
        return $this->storageKey('upload', md5($disk.'|'.$path));
    }

    protected function uploadPath(string $value, string $disk): string
    {
        try {
            $base = Storage::disk($disk)->url('');

            // Only at the start: a path containing the URL isn't that file.
            return $base !== '' && Str::startsWith($value, $base) ? Str::after($value, $base) : $value;
        } catch (\Throwable) {
            return $value;
        }
    }

    /** The original must come from a trusted source (the model), never from the request. */
    public function uploadPrune($original, $current, ?string $disk = null): array
    {
        $disk = $this->uploadDisk($disk);

        if (! array_key_exists($disk, config('filesystems.disks', []))) {
            throw new \InvalidArgumentException("The disk [{$disk}] is not configured.");
        }

        $removed = $this->uploadStoredPaths($original, $disk)->diff($this->uploadStoredPaths($current, $disk))->unique()
            ->filter(fn ($path) => Storage::disk($disk)->exists($path))
            ->values()
            ->all();

        if ($removed !== []) {
            Storage::disk($disk)->delete($removed);

            foreach ($removed as $path) {
                Cache::forget($this->uploadCacheKey($disk, $path));
            }
        }

        return $removed;
    }

    /** Any other path is someone else's file, and uploadPrune() would later delete it. */
    public function uploadKept($original, $current, ?string $disk = null): array
    {
        $disk = $this->uploadDisk($disk);

        return Collection::wrap($current)
            ->filter(fn ($file) => $file !== null && $file !== '')
            ->filter(fn ($file) => ! is_string($file) || $this->uploadIsStored($file, $original, $disk))
            ->values()
            ->all();
    }

    public function uploadIsStored(string $value, $stored, ?string $disk = null): bool
    {
        if ($value === '' || blank($stored)) {
            return false;
        }

        $disk = $this->uploadDisk($disk);

        return $this->uploadStoredPaths($stored, $disk)->contains($this->uploadPath($value, $disk));
    }

    protected function uploadStoredPaths($value, string $disk): Collection
    {
        return Collection::wrap($value)
            ->filter(fn ($file) => is_string($file) && $file !== '')
            ->map(fn ($file) => $this->uploadPath($file, $disk))
            ->values();
    }

    protected function temporaryUploadUrl(TemporaryUploadedFile $file): ?string
    {
        try {
            return $file->temporaryUrl();
        } catch (\Throwable) {
            return null;
        }
    }

    public function uploadFileTypes(): array
    {
        $types = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'video' => ['mp4', 'mov', 'webm'],
            'audio' => ['mp3', 'wav'],
            'pdf' => ['pdf'],
            'doc' => ['doc', 'docx'],
            'xls' => ['xls', 'xlsx'],
            'ppt' => ['ppt', 'pptx'],
            'archive' => ['zip', 'rar', '7z'],
            'text' => ['txt', 'md'],
            'csv' => ['csv'],
            'code' => ['json', 'js', 'ts', 'html', 'css'],
        ];

        foreach ((array) config('tallkit.upload.file_types', []) as $type => $extensions) {
            $types[$type] = array_values(array_unique([...($types[$type] ?? []), ...array_map('strtolower', (array) $extensions)]));
        }

        return $types;
    }

    public function uploadFileType(?string $extension): string
    {
        $extension = Str::lower((string) $extension);

        foreach ($this->uploadFileTypes() as $type => $extensions) {
            if (in_array($extension, $extensions, true)) {
                return $type;
            }
        }

        return 'unknown';
    }

    /** By its content for image, video and audio: a .png that is a video counts as a video. */
    public function uploadedFileType(?UploadedFile $file): string
    {
        if (! $file) {
            return 'default';
        }

        if (! $file->isValid()) {
            return $this->uploadFileType($file->getClientOriginalExtension());
        }

        $category = match (Str::before((string) $file->getMimeType(), '/')) {
            'image' => 'image',
            'video' => 'video',
            'audio' => 'audio',
            default => null,
        };

        return $category ?? $this->uploadFileType($file->getClientOriginalExtension());
    }

    public function uploadAllowedExtensions(?string $type = null): array
    {
        // Empty means the default: "none" would refuse everything and "any" would let an .html in.
        $extensions = config('tallkit.upload.allowed_extensions')
            ?: collect($this->uploadFileTypes())->except('code')->flatten()->all();

        return $type
            ? array_values(array_filter($extensions, fn ($extension) => $this->uploadFileType($extension) === $type))
            : array_values($extensions);
    }

    public function uploadMaxSize(?string $type = null, ?int $requested = null): int
    {
        $sizes = config('tallkit.upload.max_size', 20480);

        $ceiling = (int) (is_array($sizes)
            ? ($sizes[$type] ?? $sizes['default'] ?? 20480)
            : $sizes);

        return $requested > 0 ? min($requested, $ceiling) : $ceiling;
    }

    public function uploadHintText(?string $accept, ?int $maxSize, ?int $maxFiles, bool $multiple = true): ?string
    {
        $accepted = $this->uploadAcceptKinds($accept);
        $kinds = $accepted
            ? array_keys($accepted)
            : collect($this->uploadAllowedExtensions())->map(fn ($extension) => $this->uploadFileType($extension))->unique()->all();
        $sizes = collect($kinds)->map(fn ($kind) => $this->uploadMaxSize($kind, $maxSize ?: null))->unique();

        $hint = collect([
            $maxFiles ? __('Up to :count files', ['count' => $maxFiles]) : null,
            $sizes->count() === 1
                ? __($multiple ? 'max :size each' : 'max :size', ['size' => Number::fileSize($sizes->first() * 1024)])
                : null,
            $accepted ? collect($accepted)->flatten()->unique()->implode(', ') : null,
        ])->filter()->implode(' · ');

        return $hint !== '' ? $hint : null;
    }

    protected function uploadAcceptKinds(?string $accept): array
    {
        $kinds = [];

        foreach (array_filter(array_map('trim', explode(',', (string) $accept))) as $rule) {
            if (str_starts_with($rule, '.')) {
                $extension = strtolower(substr($rule, 1));
                $kinds[$this->uploadFileType($extension)][] = strtoupper($extension);
            } elseif (in_array($rule, ['image/*', 'video/*', 'audio/*'], true)) {
                $kind = Str::before($rule, '/');
                $kinds[$kind][] = match ($kind) {
                    'image' => __('Images'),
                    'video' => __('Videos'),
                    default => __('Audio'),
                };
            } elseif (str_contains($rule, '/')) {
                $extension = MimeTypes::getDefault()->getExtensions(strtolower($rule))[0] ?? null;
                $kinds[$extension ? $this->uploadFileType($extension) : 'default'][] = strtoupper($extension ?? Str::after($rule, '/'));
            }
        }

        return $kinds;
    }

    public function uploadDiskAllowed(string $disk): bool
    {
        $allowed = config('tallkit.upload.allowed_disks') ?? [$this->uploadDisk()];

        return in_array($disk, $allowed, true) && array_key_exists($disk, config('filesystems.disks', []));
    }

    // $stored: the value saved before, from the model, never from the browser. Without it, only new uploads pass.
    public function uploadRules(
        string $field,
        ?string $type = null,
        ?int $maxSize = null,
        ?int $maxFiles = null,
        bool $multiple = false,
        mixed $stored = null,
        ?string $disk = null,
    ): array {
        // Without an extension there would be no "mimes" rule and any file (a .php) would pass.
        if ($type !== null && ! array_key_exists($type, $this->uploadFileTypes())) {
            throw new \InvalidArgumentException("The upload type [{$type}] doesn't exist. Use one of: ".implode(', ', array_keys($this->uploadFileTypes())).'.');
        }

        $extensions = $this->uploadAllowedExtensions($type);

        if ($extensions === []) {
            throw new \InvalidArgumentException("No extension of the upload type [{$type}] is in tallkit.upload.allowed_extensions.");
        }

        $fileRules = [new Upload([
            'file',
            'mimes:'.implode(',', $extensions),
        ], $stored, $disk, $type, $maxSize)];

        if (! $multiple) {
            return [$field => $fileRules];
        }

        return [
            $field => array_values(array_filter(['array', $maxFiles ? "max:{$maxFiles}" : null])),
            "{$field}.*" => $fileRules,
        ];
    }
}

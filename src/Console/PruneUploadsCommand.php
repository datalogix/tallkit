<?php

namespace TALLKit\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use TALLKit\Facades\TALLKit;

class PruneUploadsCommand extends Command
{
    protected $signature = 'tallkit:prune-uploads
        {--days=1 : Only files older than this many days (a newer one may be in a form not saved yet)}
        {--disk= : The disk to look in (default: tallkit.upload.disk)}
        {--dry-run : Only list the files that would be deleted}';

    protected $description = 'Delete the files sent to the upload route (the editors\' images and videos) that nothing uses anymore';

    public function handle(): int
    {
        $disk = TALLKit::uploadDisk($this->option('disk'));

        if (! array_key_exists($disk, config('filesystems.disks', []))) {
            $this->components->error("The disk [{$disk}] is not configured.");

            return self::FAILURE;
        }

        if (! TALLKit::hasUploadsInUse()) {
            $this->components->error('Tell TALLKit which uploads are in use first, in a service provider: TALLKit::uploadsInUse(fn (array $paths, string $disk) => /* the ones your content uses */)');

            return self::FAILURE;
        }

        $before = now()->subDays(max(0, (int) $this->option('days')))->getTimestamp();
        $directory = TALLKit::uploadDirectory();
        $storage = Storage::disk($disk);

        $candidates = collect($storage->getDriver()->listContents($directory, true))
            ->filter(fn ($item) => $item->isFile())
            ->filter(fn ($file) => ($file->lastModified() ?? $storage->lastModified($file->path())) < $before)
            ->map(fn ($file) => $file->path())
            ->values();

        $unused = $candidates->chunk(500)
            ->flatMap(fn ($paths) => $paths->diff(TALLKit::uploadsInUseOf($paths->values()->all(), $disk)))
            ->values();

        foreach ($unused as $path) {
            $this->line($path);
        }

        if (! $this->option('dry-run') && $unused->isNotEmpty()) {
            $storage->delete($unused->all());
        }

        $this->components->info(sprintf(
            '%d of %d files %s.',
            $unused->count(),
            $candidates->count(),
            $this->option('dry-run') ? 'would be deleted' : 'deleted',
        ));

        return self::SUCCESS;
    }
}

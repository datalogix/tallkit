<?php

namespace TALLKit\Tests\Feature\Console;

use Illuminate\Support\Facades\Storage;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class PruneUploadsCommandTest extends TestCase
{
    protected array $asked = [];

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    protected function putOld(string $path, int $daysOld): void
    {
        Storage::disk('public')->put($path, 'x');
        touch(Storage::disk('public')->path($path), now()->subDays($daysOld)->subMinute()->getTimestamp());
    }

    protected function inUse(array $used): void
    {
        TALLKit::uploadsInUse(function (array $paths, string $disk) use ($used) {
            $this->asked[] = [$paths, $disk];

            return array_values(array_filter($used, fn ($path) => in_array(ltrim($path, '/'), $paths, true)));
        });
    }

    public function test_fails_without_the_in_use_callback(): void
    {
        $this->artisan('tallkit:prune-uploads')
            ->expectsOutputToContain('Tell TALLKit which uploads are in use first')
            ->assertFailed();
    }

    public function test_fails_on_a_disk_not_configured(): void
    {
        $this->inUse([]);

        $this->artisan('tallkit:prune-uploads', ['--disk' => 'nope'])
            ->expectsOutputToContain('The disk [nope] is not configured.')
            ->assertFailed();
    }

    public function test_deletes_old_unused_files(): void
    {
        $this->putOld('tallkit-uploads/used.png', 2);
        $this->putOld('tallkit-uploads/posts/unused.png', 2);
        $this->putOld('tallkit-uploads/new.png', 0);
        $this->putOld('other/old.png', 5);
        $this->inUse(['/tallkit-uploads/used.png']);

        $this->artisan('tallkit:prune-uploads')
            ->expectsOutput('tallkit-uploads/posts/unused.png')
            ->doesntExpectOutput('tallkit-uploads/used.png')
            ->expectsOutputToContain('1 of 2 files deleted.')
            ->assertSuccessful();

        $disk = Storage::disk('public');
        $disk->assertMissing('tallkit-uploads/posts/unused.png');
        $disk->assertExists(['tallkit-uploads/used.png', 'tallkit-uploads/new.png', 'other/old.png']);
        $this->assertSame('public', $this->asked[0][1]);
    }

    public function test_dry_run_deletes_nothing(): void
    {
        $this->putOld('tallkit-uploads/unused.png', 2);
        $this->inUse([]);

        $this->artisan('tallkit:prune-uploads', ['--dry-run' => true])
            ->expectsOutput('tallkit-uploads/unused.png')
            ->expectsOutputToContain('1 of 1 files would be deleted.')
            ->assertSuccessful();

        Storage::disk('public')->assertExists('tallkit-uploads/unused.png');
    }

    public function test_days_option(): void
    {
        $this->putOld('tallkit-uploads/a.png', 2);
        $this->putOld('tallkit-uploads/b.png', 5);
        $this->inUse([]);

        $this->artisan('tallkit:prune-uploads', ['--days' => 3])
            ->expectsOutputToContain('1 of 1 files deleted.')
            ->assertSuccessful();

        Storage::disk('public')->assertExists('tallkit-uploads/a.png');
        Storage::disk('public')->assertMissing('tallkit-uploads/b.png');

        $this->artisan('tallkit:prune-uploads', ['--days' => -5])
            ->expectsOutputToContain('1 of 1 files deleted.')
            ->assertSuccessful();

        Storage::disk('public')->assertMissing('tallkit-uploads/a.png');
    }

    public function test_uses_the_disk_option(): void
    {
        config(['filesystems.disks.other' => ['driver' => 'local', 'root' => storage_path('other')]]);
        Storage::fake('other');
        Storage::disk('other')->put('tallkit-uploads/a.png', 'x');
        touch(Storage::disk('other')->path('tallkit-uploads/a.png'), now()->subDays(3)->getTimestamp());
        $this->inUse([]);

        $this->artisan('tallkit:prune-uploads', ['--disk' => 'other'])
            ->expectsOutputToContain('1 of 1 files deleted.')
            ->assertSuccessful();

        Storage::disk('other')->assertMissing('tallkit-uploads/a.png');
        $this->assertSame('other', $this->asked[0][1]);
    }

    public function test_asks_in_chunks_of_500(): void
    {
        foreach (range(1, 501) as $i) {
            $this->putOld("tallkit-uploads/{$i}.txt", 2);
        }

        $this->inUse(['tallkit-uploads/1.txt']);

        $this->artisan('tallkit:prune-uploads', ['--dry-run' => true])
            ->expectsOutputToContain('500 of 501 files would be deleted.')
            ->assertSuccessful();

        $this->assertCount(2, $this->asked);
        $this->assertCount(500, $this->asked[0][0]);
        $this->assertCount(1, $this->asked[1][0]);
    }

    public function test_reports_nothing_to_delete(): void
    {
        $this->inUse([]);

        $this->artisan('tallkit:prune-uploads')
            ->expectsOutputToContain('0 of 0 files deleted.')
            ->assertSuccessful();
    }
}

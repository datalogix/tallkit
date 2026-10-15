<?php

namespace TALLKit\Tests\Unit\View;

use Illuminate\Filesystem\Filesystem;
use TALLKit\Tests\TestCase;
use TALLKit\View\ClassCache;

class ClassCacheTest extends TestCase
{
    protected string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir().'/tallkit-class-cache-'.bin2hex(random_bytes(4));
        mkdir($this->directory);
        config(['view.compiled' => $this->directory]);

        ClassCache::flush();
    }

    protected function tearDown(): void
    {
        ClassCache::flush();
        @chmod($this->directory, 0755);
        (new Filesystem)->deleteDirectory($this->directory);

        parent::tearDown();
    }

    protected function cacheFile(): string
    {
        return $this->directory.'/tallkit-classes.php';
    }

    public function test_merge_resolves_tailwind_conflicts(): void
    {
        $this->assertSame('p-4', ClassCache::merge('p-2', 'p-4'));
        $this->assertSame('text-red-500 p-4', ClassCache::merge('p-2 text-red-500', 'p-4'));
    }

    public function test_merge_returns_memoized_value(): void
    {
        $first = ClassCache::merge('p-2', 'p-4');

        $this->assertSame($first, ClassCache::merge('p-2', 'p-4'));
        $this->assertCount(1, SmallClassCache::state()['merged']);
    }

    public function test_path(): void
    {
        $this->assertSame($this->cacheFile(), ClassCache::path());

        config(['view.compiled' => '']);
        $this->assertNull(ClassCache::path());

        config(['view.compiled' => null]);
        $this->assertNull(ClassCache::path());

        config(['view.compiled' => $this->directory, 'tallkit.cache_classes' => false]);
        $this->assertNull(ClassCache::path());
    }

    public function test_persist_writes_file_and_new_process_reads_it(): void
    {
        $merged = ClassCache::merge('p-2', 'p-4');

        ClassCache::persist();

        $this->assertFileExists($this->cacheFile());
        $file = include $this->cacheFile();
        $this->assertIsString($file['fingerprint']);
        $this->assertSame([$merged], array_values($file['classes']));
        $this->assertSame([], glob($this->directory.'/*.tmp'));

        ClassCache::flush();
        $key = array_key_first($file['classes']);
        file_put_contents($this->cacheFile(), '<?php return '.var_export([
            'fingerprint' => $file['fingerprint'],
            'classes' => [$key => 'from-file'],
        ], true).';');

        $this->assertSame('from-file', ClassCache::merge('p-2', 'p-4'));
    }

    public function test_persist_merges_with_existing_file(): void
    {
        ClassCache::merge('p-2', 'p-4');
        ClassCache::persist();

        ClassCache::flush();
        ClassCache::merge('m-2', 'm-4');
        ClassCache::persist();

        $file = include $this->cacheFile();
        $this->assertCount(2, $file['classes']);
    }

    public function test_file_with_other_fingerprint_is_ignored(): void
    {
        ClassCache::merge('p-2', 'p-4');
        ClassCache::persist();
        $file = include $this->cacheFile();
        $key = array_key_first($file['classes']);

        file_put_contents($this->cacheFile(), '<?php return '.var_export([
            'fingerprint' => 'other',
            'classes' => [$key => 'stale'],
        ], true).';');
        ClassCache::flush();

        $this->assertSame('p-4', ClassCache::merge('p-2', 'p-4'));
    }

    public function test_invalid_file_is_ignored(): void
    {
        file_put_contents($this->cacheFile(), '<?php return "nope";');

        $this->assertSame('p-4', ClassCache::merge('p-2', 'p-4'));
    }

    public function test_persist_without_path_drops_pending(): void
    {
        config(['tallkit.cache_classes' => false]);

        ClassCache::merge('p-2', 'p-4');
        ClassCache::persist();

        $this->assertSame([], SmallClassCache::state()['pending']);
        $this->assertFileDoesNotExist($this->cacheFile());
    }

    public function test_persist_without_pending_does_nothing(): void
    {
        ClassCache::persist();

        $this->assertFileDoesNotExist($this->cacheFile());
    }

    public function test_persist_with_missing_directory_drops_pending(): void
    {
        config(['view.compiled' => $this->directory.'/missing']);

        ClassCache::merge('p-2', 'p-4');
        ClassCache::persist();

        $this->assertSame([], SmallClassCache::state()['pending']);
        $this->assertDirectoryDoesNotExist($this->directory.'/missing');
    }

    public function test_persist_skips_when_too_many_entries(): void
    {
        SmallClassCache::merge('p-1', 'm-1');
        SmallClassCache::merge('p-2', 'm-1');
        SmallClassCache::persist();
        $before = file_get_contents($this->cacheFile());

        SmallClassCache::flush();
        SmallClassCache::merge('p-3', 'm-1');
        SmallClassCache::persist();

        $this->assertSame([], SmallClassCache::state()['pending']);
        $this->assertSame($before, file_get_contents($this->cacheFile()));
    }

    public function test_put_resets_memory_when_full(): void
    {
        foreach (['p-1', 'p-2', 'p-3', 'p-4'] as $class) {
            SmallClassCache::merge($class, 'm-1');
        }

        $this->assertCount(4, SmallClassCache::state()['merged']);

        SmallClassCache::merge('p-5', 'm-1');

        $state = SmallClassCache::state();
        $this->assertCount(1, $state['merged']);
        $this->assertFalse($state['loaded']);
        $this->assertCount(2, $state['pending']);
    }

    public function test_persist_gives_up_when_file_cannot_be_written(): void
    {
        ClassCache::merge('p-2', 'p-4');
        chmod($this->directory, 0555);

        ClassCache::persist();

        chmod($this->directory, 0755);
        $this->assertFileDoesNotExist($this->cacheFile());
        $this->assertSame([], SmallClassCache::state()['pending']);
    }

    public function test_persist_removes_temporary_file_when_rename_fails(): void
    {
        mkdir($this->cacheFile());
        touch($this->cacheFile().'/keep');

        ClassCache::merge('p-2', 'p-4');
        ClassCache::persist();

        $this->assertDirectoryExists($this->cacheFile());
        $this->assertSame([], glob($this->directory.'/*.tmp'));
    }
}

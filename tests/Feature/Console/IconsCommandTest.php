<?php

namespace TALLKit\Tests\Feature\Console;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use TALLKit\Facades\TALLKit;
use TALLKit\TALLKit as TALLKitClass;
use TALLKit\Tests\TestCase;

class IconsCommandTest extends TestCase
{
    protected string $temporary;

    protected array $missing = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->temporary = sys_get_temp_dir().'/tallkit-icons-'.Str::random(8);
        File::ensureDirectoryExists($this->temporary.'/views');
        $this->app->useStoragePath($this->temporary.'/storage');

        Http::preventStrayRequests();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->temporary);

        parent::tearDown();
    }

    protected function fakeIconify(array $collections = [], bool $down = false): void
    {
        Http::fake(function (Request $request) use ($collections, $down) {
            if ($down) {
                return Http::response('', 503);
            }

            $path = parse_url($request->url(), PHP_URL_PATH);

            if ($path === '/collections') {
                return Http::response($collections);
            }

            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);
            $icons = [];

            foreach (explode(',', $query['icons'] ?? '') as $icon) {
                if (! in_array($icon, $this->missing, true)) {
                    $icons[$icon] = ['body' => '<path d="M0 0"/>'];
                }
            }

            return Http::response(['prefix' => trim($path, '/'), 'icons' => $icons, 'width' => 24, 'height' => 24]);
        });
    }

    protected function writeView(string $name, string $contents): void
    {
        File::put($this->temporary.'/views/'.$name, $contents);
    }

    public function test_fetches_the_icons_of_the_views_and_the_arguments(): void
    {
        $this->fakeIconify();
        $this->writeView('page.blade.php', implode("\n", [
            '<tk:button icon="ph:house-one" icon-trailing="mdi:arrow-one" iconOff="mdi:eye-one">x</tk:button>',
            '<tk:icon class="a" name="mdi:star-one" />',
            '<x-tallkit::icon data-x=\'>\' name="Mdi:Star-Two" />',
            '<tk:button :icon="$open ? \'mdi:open-one\' : \'mdi:close-one\'" :icon-on="$a[\'key\'] ?? \'mdi:on-one\'" />',
            '<tk:button :icon="$icons[\'skip\']" :iconOff="match($x) { \'a\' => \'mdi:match-one\' }" />',
            '@php $iconTrailing ??= \'mdi:default-one\'; @endphp',
            '@php $items = [[\'icon\' => \'mdi:array-one\'], [\'iconOn\' => $item[\'x\'] ?? \'mdi:array-two\']]; @endphp',
            '<div x-data="{ icon: \'mdi:alpine-one\' }"></div>',
            '<tk:button icon="{{ $dynamic }}" data-icon="mdi:not-an-attribute" />',
        ]));
        $this->writeView('notes.txt', '<tk:icon name="mdi:not-a-view" />');

        $this->artisan('tallkit:icons', ['icons' => ['mdi:argument-one', 'bad name!'], '--path' => [$this->temporary.'/views']])
            ->expectsOutputToContain('mdi:argument-one')
            ->assertSuccessful();

        foreach ([
            'ph:house-one', 'mdi:arrow-one', 'mdi:eye-one', 'mdi:star-one', 'mdi:star-two', 'mdi:open-one', 'mdi:close-one',
            'mdi:on-one', 'mdi:match-one', 'mdi:default-one', 'mdi:array-one', 'mdi:array-two', 'mdi:alpine-one', 'mdi:argument-one',
        ] as $icon) {
            $this->assertArrayHasKey($icon, TALLKit::iconManifest(fresh: true), $icon);
        }

        foreach (['mdi:not-an-attribute', 'mdi:not-a-view', 'a', 'key', 'skip', 'bad name!'] as $icon) {
            $this->assertArrayNotHasKey($icon, TALLKit::iconManifest(), $icon);
        }

        $this->assertFileExists(storage_path('app/tallkit/icons/mdi/argument-one.svg'));
    }

    public function test_prints_the_status_of_each_icon(): void
    {
        $this->fakeIconify();
        $this->missing = ['gone-one'];

        $this->assertSame(0, Artisan::call('tallkit:icons', ['icons' => ['mdi:found-one', 'mdi:gone-one']]));

        $output = Artisan::output();
        $this->assertMatchesRegularExpression('/mdi:found-one \.+ ok *\n/', $output);
        $this->assertMatchesRegularExpression('/mdi:gone-one \.+ not found *\n/', $output);
        $this->assertMatchesRegularExpression('/\d+ icons, 1 not found\. 1 in .+manifest\.php\./', $output);
    }

    public function test_summary_counts_the_icons(): void
    {
        $this->fakeIconify();
        $this->missing = ['gone-one'];

        Artisan::call('tallkit:icons', ['icons' => ['mdi:found-one', 'mdi:gone-one', 'mdi:found-two']]);

        $this->assertSame(0, Artisan::call('tallkit:icons', ['icons' => ['mdi:found-one', 'mdi:gone-one']]));
        $this->assertStringContainsString('1 not found. 2 in ', Artisan::output());
    }

    public function test_missing_lists_only_the_missing_icons_and_fails(): void
    {
        $this->fakeIconify();
        $this->missing = ['gone-one'];

        $this->artisan('tallkit:icons', ['icons' => ['mdi:found-one', 'mdi:gone-one'], '--missing' => true])
            ->expectsOutput('mdi:gone-one')
            ->doesntExpectOutputToContain('mdi:found-one')
            ->assertFailed();
    }

    public function test_missing_succeeds_when_every_icon_is_found(): void
    {
        $this->fakeIconify();

        $this->artisan('tallkit:icons', ['icons' => ['mdi:found-one'], '--missing' => true])
            ->doesntExpectOutputToContain('mdi:found-one')
            ->assertSuccessful();
    }

    public function test_fails_when_iconify_does_not_answer(): void
    {
        $this->fakeIconify(down: true);

        $this->assertSame(1, Artisan::call('tallkit:icons', ['icons' => ['mdi:offline-one']]));

        $output = Artisan::output();
        $this->assertMatchesRegularExpression('/mdi:offline-one \.+ iconify did not answer *\n/', $output);
        $this->assertStringContainsString('1 icons could not be checked: iconify did not answer. Run the command again in a while.', $output);
    }

    public function test_reports_when_no_icons_are_found(): void
    {
        File::partialMock()->shouldReceive('allFiles')->andReturn([]);

        $this->artisan('tallkit:icons')
            ->expectsOutputToContain('No icons found.')
            ->assertSuccessful();

        Http::assertNothingSent();
    }

    protected function bundleInto(string $directory): void
    {
        $this->app->instance(TALLKitClass::class, new class($directory) extends TALLKitClass
        {
            public function __construct(protected string $directory) {}

            public function iconBundledPath(): string
            {
                return $this->directory.'/icons.php';
            }
        });

        TALLKit::clearResolvedInstance('tallkit');
    }

    public function test_bundle_writes_the_icons_and_their_licenses(): void
    {
        $directory = $this->temporary.'/bundle';
        $this->bundleInto($directory);
        $this->fakeIconify([
            'mdi' => ['name' => 'Material Design Icons', 'author' => ['name' => 'Pictogrammers', 'url' => 'https://a.test'], 'license' => ['title' => 'Apache 2.0', 'url' => 'https://l.test']],
            'ph' => ['name' => 'Phosphor'],
        ]);

        $this->artisan('tallkit:icons', ['--bundle' => true])
            ->expectsOutputToContain('collections in '.$directory)
            ->assertSuccessful();

        $icons = include $directory.'/icons.php';

        $this->assertArrayHasKey('ph:file', $icons);
        $this->assertArrayHasKey('ph:file-pdf', $icons);
        $this->assertArrayHasKey('ph:file-image', $icons);
        $this->assertSame('<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path d="M0 0"/></svg>', $icons['ph:file']);
        $this->assertStringContainsString("// `php artisan tallkit:icons --bundle` from iconify: don't edit it", file_get_contents($directory.'/icons.php'));

        $licenses = file_get_contents($directory.'/LICENSES.md');

        $this->assertStringStartsWith("# Icon licenses\n\nThe icons in `icons.php`", $licenses);
        $this->assertStringContainsString("## Material Design Icons (`mdi`)\n\n- Author: [Pictogrammers](https://a.test)\n- License: [Apache 2.0](https://l.test)\n- Icons: `", $licenses);
        $this->assertStringContainsString("## Phosphor (`ph`)\n\n- Author: [?]()\n- License: [?]()\n- Icons: ", $licenses);
        $this->assertStringContainsString('`ph:file`, `ph:file-archive`', $licenses);
        $this->assertLessThan(strpos($licenses, '(`ph`)'), strpos($licenses, '(`mdi`)'));
        $this->assertStringEndsWith("`\n", $licenses);
    }

    public function test_bundle_finds_prefixed_names_of_known_collections(): void
    {
        $directory = $this->temporary.'/bundle';
        $this->bundleInto($directory);
        $this->fakeIconify(['mdi' => ['name' => 'MDI'], 'ph' => ['name' => 'Phosphor']]);

        $this->artisan('tallkit:icons', ['--bundle' => true])->assertSuccessful();

        $bundled = include __DIR__.'/../../../resources/icons/icons.php';
        $icons = include $directory.'/icons.php';

        $prefixed = array_filter(array_keys($bundled), fn ($name) => str_starts_with($name, 'ph:') || str_starts_with($name, 'mdi:'));
        $this->assertNotEmpty($prefixed);

        foreach ($prefixed as $name) {
            $this->assertArrayHasKey($name, $icons, $name);
        }
    }

    public function test_bundle_writes_nothing_when_iconify_does_not_answer(): void
    {
        $directory = $this->temporary.'/bundle';
        $this->bundleInto($directory);
        $this->fakeIconify(down: true);

        $this->artisan('tallkit:icons', ['--bundle' => true])
            ->expectsOutputToContain('Not written, iconify did not answer: ')
            ->assertFailed();

        $this->assertDirectoryDoesNotExist($directory);
    }

    public function test_bundle_writes_nothing_when_an_icon_is_missing(): void
    {
        $directory = $this->temporary.'/bundle';
        $this->bundleInto($directory);
        $this->fakeIconify(['ph' => ['name' => 'Phosphor']]);
        $this->missing = ['file-pdf'];

        $this->assertSame(1, Artisan::call('tallkit:icons', ['--bundle' => true]));
        $this->assertStringContainsString('Not written, these could not be fetched: ph:file-pdf.', Artisan::output());

        $this->assertDirectoryDoesNotExist($directory);
    }
}

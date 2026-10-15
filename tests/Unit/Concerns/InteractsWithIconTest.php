<?php

namespace TALLKit\Tests\Unit\Concerns;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use TALLKit\Facades\TALLKit;
use TALLKit\TALLKit as TALLKitClass;
use TALLKit\Tests\TestCase;

class InteractsWithIconTest extends TestCase
{
    private string $storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = sys_get_temp_dir().'/tallkit-icon-test-'.bin2hex(random_bytes(6));
        File::ensureDirectoryExists($this->storage);
        $this->app->useStoragePath($this->storage);

        config(['cache.default' => 'array', 'tallkit.icon.collections' => ['mdi', 'ph']]);

        $this->resetStatics();

        Http::preventStrayRequests();
    }

    protected function tearDown(): void
    {
        $this->resetStatics();
        File::deleteDirectory($this->storage);

        parent::tearDown();
    }

    private function resetStatics(): void
    {
        (new \ReflectionProperty(TALLKitClass::class, 'iconManifest'))->setValue(null, null);
        (new \ReflectionProperty(TALLKitClass::class, 'iconBundled'))->setValue(null, null);
    }

    private function iconsPath(string $path = ''): string
    {
        return $this->storage.'/app/tallkit/icons'.($path === '' ? '' : '/'.$path);
    }

    private function storeIcon(string $path, string $svg): void
    {
        File::ensureDirectoryExists(dirname($this->iconsPath($path)));
        File::put($this->iconsPath($path), $svg);
    }

    private function writeManifest(array $icons): void
    {
        File::ensureDirectoryExists($this->iconsPath());
        File::put($this->iconsPath('manifest.php'), '<?php return '.var_export($icons, true).';');
    }

    private function manifestOnDisk(): array
    {
        return include $this->iconsPath('manifest.php');
    }

    private function sanitize(string $svg): ?string
    {
        return (new \ReflectionMethod(TALLKitClass::class, 'iconSanitize'))->invoke(app('tallkit'), $svg);
    }

    private function svg(string $body, string $viewBox = '0 0 24 24'): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="'.$viewBox.'">'.$body.'</svg>';
    }

    public function test_icon_name_is_valid(): void
    {
        $this->assertTrue(TALLKit::iconNameIsValid('home'));
        $this->assertTrue(TALLKit::iconNameIsValid('MDI:Home-Outline'));
        $this->assertTrue(TALLKit::iconNameIsValid('material-symbols:home_rounded'));
        $this->assertTrue(TALLKit::iconNameIsValid('icon-2'));
        $this->assertFalse(TALLKit::iconNameIsValid(''));
        $this->assertFalse(TALLKit::iconNameIsValid('../etc/passwd'));
        $this->assertFalse(TALLKit::iconNameIsValid('a:b:c'));
        $this->assertFalse(TALLKit::iconNameIsValid('home-'));
        $this->assertFalse(TALLKit::iconNameIsValid('-home'));
        $this->assertFalse(TALLKit::iconNameIsValid('mdi_x:home'));
        $this->assertFalse(TALLKit::iconNameIsValid('home icon'));
    }

    public function test_icon_svg_rejects_empty_and_invalid_names(): void
    {
        $this->assertNull(TALLKit::iconSvg(null));
        $this->assertNull(TALLKit::iconSvg(''));
        $this->assertNull(TALLKit::iconSvg('bad/name'));
        $this->assertCount(0, app('tallkit.icons'));
    }

    public function test_icon_svg_returns_bundled_icon_without_network(): void
    {
        $bundled = include TALLKit::iconBundledPath();

        $this->assertSame($bundled['alert-outline'], TALLKit::iconSvg('alert-outline'));
        $this->assertSame($bundled['alert-outline'], TALLKit::iconSvg('Alert-Outline'));
        $this->assertSame($bundled['alert-outline'], app('tallkit.icons')['alert-outline']);
        $this->assertFileDoesNotExist(TALLKit::iconManifestPath());
    }

    public function test_icon_svg_memoizes_per_request_and_retry_bypasses_it(): void
    {
        app('tallkit.icons')['alert-outline'] = '<svg>seen</svg>';

        $this->assertSame('<svg>seen</svg>', TALLKit::iconSvg('alert-outline'));
        $this->assertStringContainsString('viewBox', TALLKit::iconSvg('alert-outline', retry: true));
        $this->assertStringContainsString('viewBox', app('tallkit.icons')['alert-outline']);
    }

    public function test_icon_bundled_path_points_to_resources(): void
    {
        $this->assertSame(realpath(__DIR__.'/../../../resources/icons/icons.php'), realpath(TALLKit::iconBundledPath()));
    }

    public function test_bundled_icons_are_empty_when_file_is_missing(): void
    {
        Http::fake(['*' => Http::response('404')]);

        $kit = new class extends TALLKitClass
        {
            public function iconBundledPath(): string
            {
                return '/nonexistent/icons.php';
            }
        };

        $this->assertSame(['status' => 'missing', 'svg' => null], $kit->iconLookup(['alert-outline'])['alert-outline']);
    }

    public function test_lookup_marks_invalid_names_as_missing(): void
    {
        $this->assertSame(
            ['bad name' => ['status' => 'missing', 'svg' => null]],
            TALLKit::iconLookup(['bad name', 'bad name']),
        );
    }

    public function test_lookup_reads_manifest_case_insensitively(): void
    {
        $this->writeManifest(['mdi:home' => '<svg>m</svg>']);

        $this->assertSame(['MDI:Home' => ['status' => 'found', 'svg' => '<svg>m</svg>']], TALLKit::iconLookup(['MDI:Home']));
    }

    public function test_icon_manifest_path_and_contents(): void
    {
        $this->assertSame($this->iconsPath('manifest.php'), TALLKit::iconManifestPath());
        $this->assertSame([], TALLKit::iconManifest());

        $this->writeManifest(['a:b' => '<svg/>']);

        $this->assertSame([], TALLKit::iconManifest());
        $this->assertSame(['a:b' => '<svg/>'], TALLKit::iconManifest(fresh: true));
        $this->assertSame(['a:b' => '<svg/>'], TALLKit::iconManifest());
    }

    public function test_lookup_reads_stored_svg_and_remembers_it(): void
    {
        $this->writeManifest(['mdi:other' => '<svg>o</svg>']);
        $this->storeIcon('ph/home.svg', $this->svg('<path d="M0 0"/>'));

        $result = TALLKit::iconLookup(['Home']);

        $this->assertSame('found', $result['Home']['status']);
        $this->assertSame($this->svg('<path d="M0 0"/>'), $result['Home']['svg']);
        $this->assertSame(['home' => $result['Home']['svg'], 'mdi:other' => '<svg>o</svg>'], $this->manifestOnDisk());
        $this->assertSame($this->manifestOnDisk(), TALLKit::iconManifest());
        $this->assertFileExists($this->iconsPath('manifest.php.lock'));
        $this->assertSame([], glob($this->iconsPath('*.tmp')));
    }

    public function test_lookup_skips_names_cached_as_missing(): void
    {
        Cache::put('tallkit.icon.missing:home', true);

        $this->assertSame(['status' => 'missing', 'svg' => null], TALLKit::iconLookup(['HOME'])['HOME']);
        Http::assertNothingSent();
    }

    public function test_lookup_fails_fast_while_api_is_unavailable(): void
    {
        Cache::put('tallkit.icon.unavailable', true);

        $this->assertSame(
            ['mdi:a' => ['status' => 'failed', 'svg' => null], 'mdi:b' => ['status' => 'failed', 'svg' => null]],
            TALLKit::iconLookup(['mdi:a', 'mdi:b']),
        );
        Http::assertNothingSent();
    }

    public function test_lookup_fetches_from_iconify_and_stores_the_svg(): void
    {
        Http::fake([
            'https://api.iconify.design/mdi.json*' => Http::response('404'),
            'https://api.iconify.design/ph.json*' => Http::response([
                'width' => 32,
                'icons' => [
                    'home' => ['body' => '<path d="M1 1"/>', 'left' => 1, 'top' => 2, 'height' => 20],
                ],
            ]),
        ]);

        $result = TALLKit::iconLookup(['home']);
        $svg = $this->svg('<path d="M1 1"/>', '1 2 32 20');

        $this->assertSame(['home' => ['status' => 'found', 'svg' => $svg, 'collection' => 'ph']], $result);
        $this->assertSame($svg, File::get($this->iconsPath('ph/home.svg')));
        $this->assertSame(['home' => $svg], $this->manifestOnDisk());
        $this->assertFalse(Cache::has('tallkit.icon.unavailable'));

        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.iconify.design/ph.json?icons=home');
        Http::assertSentCount(2);
    }

    public function test_fetch_groups_icons_by_collection(): void
    {
        Http::fake(['*' => Http::response(['icons' => [
            'a' => ['body' => '<g/>'],
            'b' => ['body' => '<g/>'],
        ]])]);

        $result = TALLKit::iconLookup(['mdi:a', 'mdi:b']);

        $this->assertSame($this->svg('<g/>', '0 0 16 16'), $result['mdi:a']['svg']);
        $this->assertSame('found', $result['mdi:b']['status']);
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request) => $request['icons'] === 'a,b');
    }

    public function test_icon_not_in_any_collection_is_cached_as_missing(): void
    {
        config(['tallkit.icon.missing_ttl' => 10]);
        Http::fake(['*' => Http::response(['icons' => ['x' => 'not-an-array', 'y' => ['nobody' => true]]])]);

        $this->assertSame(['status' => 'missing', 'svg' => null], TALLKit::iconLookup(['mdi:x'])['mdi:x']);
        $this->assertSame(['status' => 'missing', 'svg' => null], TALLKit::iconLookup(['mdi:y'])['mdi:y']);
        $this->assertSame(['status' => 'missing', 'svg' => null], TALLKit::iconLookup(['mdi:z'])['mdi:z']);
        $this->assertTrue(Cache::has('tallkit.icon.missing:mdi:x'));
        $this->assertFileDoesNotExist(TALLKit::iconManifestPath());

        $this->travel(11)->seconds();

        $this->assertFalse(Cache::has('tallkit.icon.missing:mdi:x'));
    }

    public function test_retry_and_refresh_bypass_caches(): void
    {
        Http::fake(['*' => Http::response(['icons' => ['home' => ['body' => '<g/>']]])]);
        Cache::put('tallkit.icon.missing:mdi:home', true);
        Cache::put('tallkit.icon.unavailable', true);

        $this->assertSame('found', TALLKit::iconLookup(['mdi:home'], retry: true)['mdi:home']['status']);

        $this->writeManifest(['mdi:home' => '<svg>old</svg>']);
        File::put($this->iconsPath('mdi/home.svg'), '<svg>old</svg>');
        TALLKit::iconManifest(fresh: true);

        $refreshed = TALLKit::iconLookup(['mdi:home'], refresh: true)['mdi:home'];

        $this->assertSame($this->svg('<g/>', '0 0 16 16'), $refreshed['svg']);
        $this->assertSame($refreshed['svg'], $this->manifestOnDisk()['mdi:home']);
        Http::assertSentCount(2);
    }

    public function test_unsuccessful_response_fails_and_marks_api_unavailable(): void
    {
        Http::fake(['*' => Http::response('error', 500)]);

        $this->assertSame(['status' => 'failed', 'svg' => null], TALLKit::iconLookup(['mdi:home'])['mdi:home']);
        $this->assertTrue(Cache::has('tallkit.icon.unavailable'));

        $this->travel(61)->seconds();

        $this->assertFalse(Cache::has('tallkit.icon.unavailable'));
    }

    public function test_non_json_response_fails(): void
    {
        Http::fake(['*' => Http::response('not json')]);

        $this->assertSame(['status' => 'failed', 'svg' => null], TALLKit::iconLookup(['mdi:home'])['mdi:home']);
        $this->assertTrue(Cache::has('tallkit.icon.unavailable'));
    }

    public function test_connection_failure_fails(): void
    {
        Http::fake(['*' => Http::failedConnection()]);

        $this->assertSame(['status' => 'failed', 'svg' => null], TALLKit::iconLookup(['mdi:home'])['mdi:home']);
        $this->assertTrue(Cache::has('tallkit.icon.unavailable'));
    }

    public function test_pool_exception_is_reported_and_fails(): void
    {
        $exception = new \RuntimeException('pool exploded');
        $reported = [];
        $this->app->make(ExceptionHandler::class)
            ->reportable(function (\RuntimeException $e) use (&$reported) {
                $reported[] = $e;

                return false;
            });

        Http::shouldReceive('pool')->once()->andThrow($exception);

        $this->assertSame(['status' => 'failed', 'svg' => null], TALLKit::iconLookup(['mdi:home'])['mdi:home']);
        $this->assertSame([$exception], $reported);
    }

    public function test_failed_collection_stops_the_candidate_search(): void
    {
        Http::fake([
            'https://api.iconify.design/mdi.json*' => Http::response('', 503),
            'https://api.iconify.design/ph.json*' => Http::response(['icons' => ['home' => ['body' => '<g/>']]]),
        ]);

        $this->assertSame(['status' => 'failed', 'svg' => null], TALLKit::iconLookup(['home'])['home']);
        $this->assertFalse(Cache::has('tallkit.icon.missing:home'));
    }

    public function test_aliases_resolve_to_the_parent_icon(): void
    {
        Http::fake(['*' => Http::response([
            'icons' => ['home' => ['body' => '<g/>']],
            'aliases' => ['house' => ['parent' => 'cabin'], 'cabin' => ['parent' => 'home']],
        ])]);

        $this->assertSame($this->svg('<g/>', '0 0 16 16'), TALLKit::iconLookup(['mdi:house'])['mdi:house']['svg']);
    }

    public function test_alias_loop_gives_up(): void
    {
        Http::fake(['*' => Http::response([
            'icons' => [],
            'aliases' => ['a' => ['parent' => 'b'], 'b' => ['parent' => 'a']],
        ])]);

        $this->assertSame(['status' => 'missing', 'svg' => null], TALLKit::iconLookup(['mdi:a'])['mdi:a']);
    }

    public function test_transformed_alias_and_icon_are_fetched_as_svg(): void
    {
        Http::fake([
            'https://api.iconify.design/mdi.json*' => Http::response([
                'icons' => [
                    'home' => ['body' => '<g/>'],
                    'turned' => ['body' => '<g/>', 'rotate' => 1],
                ],
                'aliases' => ['flipped' => ['parent' => 'home', 'hFlip' => true]],
            ]),
            'https://api.iconify.design/mdi:flipped.svg' => Http::response($this->svg('<g id="flipped"/>')),
            'https://api.iconify.design/mdi:turned.svg' => Http::response($this->svg('<g id="turned"/>')),
        ]);

        $result = TALLKit::iconLookup(['mdi:flipped', 'mdi:turned']);

        $this->assertSame($this->svg('<g id="flipped"/>'), $result['mdi:flipped']['svg']);
        $this->assertSame($this->svg('<g id="turned"/>'), $result['mdi:turned']['svg']);
        $this->assertSame($this->svg('<g id="flipped"/>'), File::get($this->iconsPath('mdi/flipped.svg')));
    }

    public function test_svg_api_failures_fail_the_icon(): void
    {
        Http::fake([
            'https://api.iconify.design/mdi.json*' => Http::response(['icons' => [
                'a' => ['body' => '<g/>', 'vFlip' => true],
                'b' => ['body' => '<g/>', 'vFlip' => true],
            ]]),
            'https://api.iconify.design/mdi:a.svg' => Http::response('<SVG>', 404),
            'https://api.iconify.design/mdi:b.svg' => Http::response('nothing'),
        ]);

        $this->assertSame(
            ['mdi:a' => ['status' => 'failed', 'svg' => null], 'mdi:b' => ['status' => 'failed', 'svg' => null]],
            TALLKit::iconLookup(['mdi:a', 'mdi:b']),
        );
    }

    public function test_svg_api_connection_failure_is_reported(): void
    {
        $reported = [];
        $this->app->make(ExceptionHandler::class)
            ->reportable(function (\Throwable $e) use (&$reported) {
                $reported[] = $e;

                return false;
            });

        Http::fake([
            'https://api.iconify.design/mdi.json*' => Http::response(['icons' => ['a' => ['body' => '<g/>', 'hFlip' => true]]]),
            'https://api.iconify.design/mdi:a.svg' => Http::failedConnection(),
        ]);

        $this->assertSame(['status' => 'failed', 'svg' => null], TALLKit::iconLookup(['mdi:a'])['mdi:a']);
        $this->assertCount(1, $reported);
        $this->assertInstanceOf(ConnectionException::class, $reported[0]);
    }

    public function test_unsafe_svg_body_fails(): void
    {
        Http::fake(['*' => Http::response(['icons' => ['a' => ['body' => '<g>']]])]);

        $this->assertSame(['status' => 'failed', 'svg' => null], TALLKit::iconLookup(['mdi:a'])['mdi:a']);
        $this->assertFileDoesNotExist($this->iconsPath('mdi/a.svg'));
    }

    public function test_rebuild_manifest_from_stored_files(): void
    {
        $this->assertSame(0, TALLKit::iconRebuildManifest());
        $this->assertSame([], $this->manifestOnDisk());

        $this->writeManifest(['stale:icon' => '<svg/>']);
        $this->storeIcon('mdi/home.svg', $this->svg('<g/>'));
        $this->storeIcon('ph/sub/deep.svg', $this->svg('<g/>'));
        $this->storeIcon('mdi/broken.svg', '<svg>');
        $this->storeIcon('mdi/notes.txt', $this->svg('<g/>'));

        $this->assertSame(2, TALLKit::iconRebuildManifest());
        $this->assertSame(['mdi:home' => $this->svg('<g/>'), 'ph:sub:deep' => $this->svg('<g/>')], $this->manifestOnDisk());
        $this->assertSame($this->manifestOnDisk(), TALLKit::iconManifest());
    }

    public function test_sanitize_rejects_invalid_documents(): void
    {
        $this->assertNull($this->sanitize('not xml'));
        $this->assertNull($this->sanitize('<svg>'));
        $this->assertNull($this->sanitize('<html><body/></html>'));
        $this->assertNull($this->sanitize('<!DOCTYPE svg [<!ENTITY x "y">]><svg>&x;</svg>'));
        $this->assertNull($this->sanitize('<!DOCTYPE svg><svg/>'));
    }

    public function test_sanitize_removes_dangerous_nodes(): void
    {
        $svg = '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg">'
            .'<?php echo 1; ?><!-- comment -->'
            .'<script>alert(1)</script><SCRIPT>alert(2)</SCRIPT>'
            .'<foreignObject><div>x</div></foreignObject>'
            .'<title>T<b>bold</b></title><desc>D<span>s</span></desc>'
            .'<iframe/><handler/><img/>'
            .'<g><path d="M0 0"/></g>'
            .'</svg>';

        $this->assertSame(
            '<svg xmlns="http://www.w3.org/2000/svg"><title>T</title><desc>D</desc><g><path d="M0 0"/></g></svg>',
            $this->sanitize($svg),
        );
    }

    public function test_sanitize_removes_external_styles(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg">'
            .'<style>@import "x.css";</style>'
            .'<style>.a{fill:url( "https://evil")}</style>'
            .'<STYLE>.b{fill:url(&#104;ttp://evil)}</STYLE>'
            .'<style>.c{fill:url(#grad)}</style>'
            .'<style>.d{fill:red}</style>'
            .'</svg>';

        $this->assertSame(
            '<svg xmlns="http://www.w3.org/2000/svg"><style>.c{fill:url(#grad)}</style><style>.d{fill:red}</style></svg>',
            $this->sanitize($svg),
        );
    }

    public function test_sanitize_removes_dangerous_attributes(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" onload="x()">'
            .'<a href="java&#x09;script:alert(1)" ONCLICK="y()"/>'
            .'<a xlink:href=" JavaScript:alert(1)"/>'
            .'<set attributeName="href" values="0;javascript:alert(1)"/>'
            .'<a href="data:text/html;base64,xx"/>'
            .'<a href="vbscript:x"/>'
            .'<a href="#ok" fill="red"/>'
            .'</svg>';

        $this->assertSame(
            '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'
            .'<a/><a/><set attributeName="href"/><a/><a/><a href="#ok" fill="red"/></svg>',
            $this->sanitize($svg),
        );
    }

    public function test_sanitize_keeps_cdata_as_text(): void
    {
        $this->assertSame(
            '<svg xmlns="http://www.w3.org/2000/svg"><style>.a{fill:red}</style></svg>',
            $this->sanitize('<svg xmlns="http://www.w3.org/2000/svg"><style><![CDATA[.a{fill:red}]]></style></svg>'),
        );
    }

    public function test_sanitize_restores_libxml_error_mode(): void
    {
        $previous = libxml_use_internal_errors(false);

        $this->sanitize('<svg>');

        $this->assertFalse(libxml_use_internal_errors($previous));
        $this->assertFalse(libxml_get_last_error());
    }
}

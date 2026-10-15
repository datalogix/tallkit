<?php

namespace TALLKit\Tests\Unit\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithUrlTest extends TestCase
{
    protected string $publicPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publicPath = sys_get_temp_dir().'/tallkit-url-test-'.uniqid();
        File::ensureDirectoryExists($this->publicPath.'/images');
        $this->app->usePublicPath($this->publicPath);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->publicPath);

        parent::tearDown();
    }

    public function test_route_detect(): void
    {
        Route::get('/dashboard/{id?}', fn () => '')->name('dashboard');
        app('router')->getRoutes()->refreshNameLookups();

        $this->assertSame('http://localhost/dashboard/5', TALLKit::routeDetect(['missing', null, 'dashboard'], ['id' => 5]));
        $this->assertSame('http://localhost/dashboard', TALLKit::routeDetect('dashboard'));
        $this->assertSame('/', TALLKit::routeDetect('missing'));
        $this->assertSame('/home', TALLKit::routeDetect(null, default: '/home'));
        $this->assertNull(TALLKit::routeDetect([], default: null));
    }

    public function test_find_asset(): void
    {
        File::put($this->publicPath.'/logo.svg', '<svg/>');

        $this->assertSame('http://localhost/logo.svg', TALLKit::findAsset(['', 'missing.svg', 'logo.svg']));
        $this->assertSame('http://localhost/logo.svg', TALLKit::findAsset('logo.svg'));
        $this->assertNull(TALLKit::findAsset('missing.svg'));
    }

    public function test_find_image_is_memoized(): void
    {
        $this->assertNull(TALLKit::findImage('logo'));

        File::put($this->publicPath.'/images/logo.jpg', 'x');

        $this->assertNull(TALLKit::findImage('logo'));
        $this->assertSame('http://localhost/images/logo.jpg', TALLKit::findImage('logo', ['images/']));

        $this->app->forgetScopedInstances();

        $this->assertSame('http://localhost/images/logo.jpg', TALLKit::findImage('logo'));
        $this->assertNull(TALLKit::findImage('logo', extensions: ['png']));
    }

    public function test_safe_url(): void
    {
        $this->assertSame('https://example.com/a', TALLKit::safeUrl(' https://example.com/a '));
        $this->assertSame('HTTP://example.com', TALLKit::safeUrl('HTTP://example.com'));
        $this->assertSame('/path?x=1', TALLKit::safeUrl('/path?x=1'));
        $this->assertNull(TALLKit::safeUrl("java\tscript:alert(1)"));
        $this->assertNull(TALLKit::safeUrl('mailto:a@b.c'));
        $this->assertNull(TALLKit::safeUrl("\x00 "));
        $this->assertNull(TALLKit::safeUrl(null));
    }

    public function test_safe_href(): void
    {
        $this->assertSame('mailto:a@b.c', TALLKit::safeHref('mailto:a@b.c'));
        $this->assertSame(' /a ', TALLKit::safeHref(' /a '));
        $this->assertSame('#top', TALLKit::safeHref(str('#top')));
        $this->assertNull(TALLKit::safeHref(' JavaScript:alert(1)'));
        $this->assertNull(TALLKit::safeHref("jav\nascript:alert(1)"));
        $this->assertNull(TALLKit::safeHref('vbscript:x'));
        $this->assertNull(TALLKit::safeHref('data:text/html,x'));
        $this->assertNull(TALLKit::safeHref(''));
        $this->assertNull(TALLKit::safeHref(null));
    }

    public function test_is_current_href_by_path(): void
    {
        $this->withRequest('http://localhost/users/5');

        $this->assertTrue(TALLKit::isCurrentHref('/users'));
        $this->assertTrue(TALLKit::isCurrentHref('http://localhost/users/5'));
        $this->assertTrue(TALLKit::isCurrentHref('/users/5/'));
        $this->assertFalse(TALLKit::isCurrentHref('/users', exact: true));
        $this->assertTrue(TALLKit::isCurrentHref('/users/5', exact: true));
        $this->assertFalse(TALLKit::isCurrentHref('/posts'));
        $this->assertFalse(TALLKit::isCurrentHref('/'));
        $this->assertFalse(TALLKit::isCurrentHref(null));
        $this->assertFalse(TALLKit::isCurrentHref(''));
        $this->assertFalse(TALLKit::isCurrentHref('?tab=a'));
        $this->assertFalse(TALLKit::isCurrentHref('https://other.com/users'));
    }

    public function test_is_current_href_home(): void
    {
        $this->withRequest('http://localhost/');

        $this->assertTrue(TALLKit::isCurrentHref('/'));
        $this->assertTrue(TALLKit::isCurrentHref('http://localhost'));
    }

    public function test_is_current_href_decodes_path(): void
    {
        $this->withRequest('http://localhost/caf%C3%A9');

        $this->assertTrue(TALLKit::isCurrentHref('/caf%C3%A9'));
    }

    public function test_is_current_href_with_app_url_host_and_base_path(): void
    {
        config(['app.url' => 'https://app.test/sub']);
        $this->withRequest('http://localhost/sub/users', '/sub');

        $this->assertTrue(TALLKit::isCurrentHref('https://app.test/sub/users'));
        $this->assertTrue(TALLKit::isCurrentHref('/sub/users'));
        $this->assertFalse(TALLKit::isCurrentHref('/sub'));
        $this->assertFalse(TALLKit::isCurrentHref('/subway/users'));
    }

    public function test_is_current_href_base_path_root(): void
    {
        config(['app.url' => 'https://app.test/sub']);
        $this->withRequest('http://localhost/sub/', '/sub');

        $this->assertTrue(TALLKit::isCurrentHref('/sub'));
    }

    public function test_is_current_href_with_query(): void
    {
        $this->withRequest('http://localhost/users?tab=a&page=2&ids[]=1&ids[]=2&obj[a]=1');

        $this->assertTrue(TALLKit::isCurrentHref('/users?tab=a'));
        $this->assertTrue(TALLKit::isCurrentHref('/users?tab=a&page=2'));
        $this->assertTrue(TALLKit::isCurrentHref('/users?ids[]=1&ids[]=2'));
        $this->assertFalse(TALLKit::isCurrentHref('/users?ids[]=1'));
        $this->assertFalse(TALLKit::isCurrentHref('/users?tab=b'));
        $this->assertFalse(TALLKit::isCurrentHref('/users?missing=1'));
        $this->assertFalse(TALLKit::isCurrentHref('/users?obj=1'));
        $this->assertTrue(TALLKit::isCurrentHref('/users?tab=b', query: false));
        $this->assertTrue(TALLKit::isCurrentHref('/users?tab=a', query: true));
    }

    public function test_is_current_href_on_livewire_request(): void
    {
        $request = Request::create('http://localhost/livewire/update', 'POST', [
            'components' => [['snapshot' => json_encode(['memo' => ['path' => 'caf%C3%A9/menu']])]],
        ]);
        $request->headers->set('X-Livewire', '1');
        $request->headers->set('referer', 'http://localhost/caf%C3%A9/menu?tab=a');
        $this->app->instance('request', $request);

        $this->assertTrue(TALLKit::isCurrentHref('/café'));
        $this->assertTrue(TALLKit::isCurrentHref('/café/menu', exact: true));
        $this->assertTrue(TALLKit::isCurrentHref('/café/menu?tab=a'));
        $this->assertFalse(TALLKit::isCurrentHref('/café/menu?tab=b'));
        $this->assertFalse(TALLKit::isCurrentHref('/livewire/update'));
    }

    protected function withRequest(string $url, string $base = ''): void
    {
        $this->app->instance('request', Request::create($url, server: $base === '' ? [] : [
            'SCRIPT_FILENAME' => '/var/www'.$base.'/index.php',
            'SCRIPT_NAME' => $base.'/index.php',
            'PHP_SELF' => $base.'/index.php',
        ]));
    }
}

<?php

namespace TALLKit\Tests\Unit\Concerns;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Defer\DeferredCallbackCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Http;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithAvatarTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    protected function key(string $value): string
    {
        return TALLKit::storageKey('avatar', hash('xxh128', $value));
    }

    protected function runDeferred(): void
    {
        app(DeferredCallbackCollection::class)->invoke();
    }

    public function test_avatar_url_empty_and_url_values(): void
    {
        $this->assertNull(TALLKit::avatarUrl(null));
        $this->assertNull(TALLKit::avatarUrl(''));
        $this->assertSame('https://example.com/a.png', TALLKit::avatarUrl('https://example.com/a.png'));
    }

    public function test_avatar_url_without_unavatar(): void
    {
        config(['tallkit.avatar.unavatar' => false]);

        $this->assertNull(TALLKit::avatarUrl('jane@example.com'));
        $this->assertCount(0, app(DeferredCallbackCollection::class));
    }

    public function test_avatar_url_from_cache(): void
    {
        Cache::put($this->key('jane@example.com'), 'https://cdn.test/jane.png');
        Cache::put($this->key('none@example.com'), '');

        $this->assertSame('https://cdn.test/jane.png', TALLKit::avatarUrl('jane@example.com'));
        $this->assertNull(TALLKit::avatarUrl('none@example.com'));

        Cache::flush();

        $this->assertSame('https://cdn.test/jane.png', TALLKit::avatarUrl('jane@example.com'));
        $this->assertCount(0, app(DeferredCallbackCollection::class));
    }

    public function test_avatar_url_looks_up_after_response(): void
    {
        Http::fake([
            'unavatar.io/found%40example.com*' => Http::response(['url' => 'https://cdn.test/found.png']),
            'unavatar.io/fallback%40example.com*' => Http::response(['url' => 'https://unavatar.io/FALLBACK.png']),
            'unavatar.io/empty%40example.com*' => Http::response([]),
            'unavatar.io/missing%40example.com*' => Http::response('', 404),
            'unavatar.io/broken%40example.com*' => Http::response('', 500),
        ]);

        $values = ['found@example.com', 'fallback@example.com', 'empty@example.com', 'missing@example.com', 'broken@example.com'];

        foreach ($values as $value) {
            $this->assertNull(TALLKit::avatarUrl($value, $value === 'found@example.com' ? 120 : null));
        }

        $this->assertNull(TALLKit::avatarUrl('found@example.com'));
        $this->assertCount(1, app(DeferredCallbackCollection::class));
        Http::assertNothingSent();

        $this->runDeferred();

        Http::assertSentCount(5);
        Http::assertSent(fn (Request $request) => $request->url() === 'https://unavatar.io/found%40example.com?json');

        $this->assertSame('https://cdn.test/found.png', Cache::get($this->key('found@example.com')));
        $this->assertSame('', Cache::get($this->key('fallback@example.com')));
        $this->assertSame('', Cache::get($this->key('empty@example.com')));
        $this->assertSame('', Cache::get($this->key('missing@example.com')));
        $this->assertSame('', Cache::get($this->key('broken@example.com')));
        $this->assertCount(0, app('tallkit.avatar-lookups'));

        $this->app->forgetScopedInstances();

        $this->assertSame('https://cdn.test/found.png', TALLKit::avatarUrl('found@example.com'));
        $this->assertNull(TALLKit::avatarUrl('missing@example.com'));
    }

    public function test_avatar_lookup_ttls(): void
    {
        Http::fake([
            'unavatar.io/found*' => Http::response(['url' => 'https://cdn.test/found.png']),
            'unavatar.io/custom*' => Http::response(['url' => 'https://cdn.test/custom.png']),
            'unavatar.io/missing*' => Http::response('', 404),
            'unavatar.io/broken*' => Http::response('', 503),
        ]);

        $store = \Mockery::mock(Cache::store())->makePartial();
        Cache::shouldReceive('store')->andReturn($store);

        $store->shouldReceive('put')->once()->with($this->key('found'), 'https://cdn.test/found.png', 60 * 60 * 24 * 30);
        $store->shouldReceive('put')->once()->with($this->key('custom'), 'https://cdn.test/custom.png', 300);
        $store->shouldReceive('put')->once()->with($this->key('missing'), '', 60 * 60 * 24 * 30);
        $store->shouldReceive('put')->once()->with($this->key('broken'), '', 60 * 60);

        TALLKit::avatarUrl('found');
        TALLKit::avatarUrl('custom', 300);
        TALLKit::avatarUrl('missing');
        TALLKit::avatarUrl('broken');

        $this->runDeferred();
    }

    public function test_avatar_lookup_skips_pending_values(): void
    {
        Cache::add(TALLKit::storageKey('avatar', 'pending', hash('xxh128', 'jane')), true, 60);

        $this->assertNull(TALLKit::avatarUrl('jane'));
        $this->assertCount(0, app('tallkit.avatar-lookups'));
        $this->assertCount(0, app(DeferredCallbackCollection::class));
    }

    public function test_avatar_lookups_run_in_chunks(): void
    {
        Http::fake(['unavatar.io/*' => Http::response('', 404)]);

        foreach (range(1, 12) as $i) {
            TALLKit::avatarUrl("user{$i}");
        }

        $this->assertCount(12, app('tallkit.avatar-lookups'));

        $this->runDeferred();

        Http::assertSentCount(12);
        $this->assertSame('', Cache::get($this->key('user12')));
    }

    public function test_avatar_lookup_reports_connection_failures(): void
    {
        Exceptions::fake();
        Http::fake(['unavatar.io/*' => Http::failedConnection('timeout')]);

        TALLKit::avatarUrl('jane');

        $this->runDeferred();

        Exceptions::assertReported(ConnectionException::class);
        $this->assertSame('', Cache::get($this->key('jane')));
    }

    public function test_avatar_lookup_without_response(): void
    {
        Exceptions::fake();

        $result = (fn () => $this->avatarLookup(null))->call(app('tallkit'));

        $this->assertSame([null, false], $result);
        Exceptions::assertNothingReported();
    }

    public function test_avatar_initials(): void
    {
        $this->assertSame('JD', TALLKit::avatarInitials('John Doe'));
        $this->assertSame('JS', TALLKit::avatarInitials('john michael smith'));
        $this->assertSame('JD', TALLKit::avatarInitials('john.doe@example.com'));
        $this->assertSame('JD', TALLKit::avatarInitials('john_doe@example.com'));
        $this->assertSame('Jo', TALLKit::avatarInitials('john'));
        $this->assertSame('J', TALLKit::avatarInitials('John Doe', true));
        $this->assertSame('A', TALLKit::avatarInitials('a'));
        $this->assertSame('ÉS', TALLKit::avatarInitials('élodie silva'));
        $this->assertNull(TALLKit::avatarInitials(''));
        $this->assertNull(TALLKit::avatarInitials(null));
        $this->assertNull(TALLKit::avatarInitials('@example.com'));
    }
}

<?php

namespace TALLKit\Tests\Feature\Assets;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use TALLKit\Tests\TestCase;

class AssetControllerTest extends TestCase
{
    protected function dist(string $file): string
    {
        return realpath(__DIR__.'/../../../dist/'.$file);
    }

    public function test_serves_the_full_script_in_debug_without_cache_headers(): void
    {
        config(['app.debug' => true]);

        $response = $this->get('/tallkit/tallkit.js')->assertOk();

        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
        $this->assertSame($this->dist('tallkit.js'), $response->baseResponse->getFile()->getRealPath());
        $this->assertSame('application/javascript; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertNull($response->headers->get('Expires'));
        $this->assertFalse($response->headers->hasCacheControlDirective('immutable'));
    }

    public function test_serves_the_minified_script_with_cache_headers(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/tallkit/tallkit.js')->assertOk();

        $this->assertSame($this->dist('tallkit.min.js'), $response->baseResponse->getFile()->getRealPath());
        $this->assertSame('application/javascript; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertTrue($response->headers->hasCacheControlDirective('immutable'));
        $this->assertSame('31536000', $response->headers->getCacheControlDirective('max-age'));
        $this->assertSame(
            gmdate('D, d M Y H:i:s', filemtime($this->dist('tallkit.min.js'))).' GMT',
            $response->headers->get('Last-Modified'),
        );
        $this->assertGreaterThan(strtotime('+364 days'), strtotime($response->headers->get('Expires')));
    }

    public function test_answers_not_modified_when_the_file_did_not_change(): void
    {
        config(['app.debug' => false]);
        $lastModified = gmdate('D, d M Y H:i:s', filemtime($this->dist('tallkit.min.js'))).' GMT';

        $response = $this->get('/tallkit/tallkit.js', ['If-Modified-Since' => $lastModified]);

        $response->assertStatus(304);
        $this->assertSame('', $response->getContent());
        $this->assertTrue($response->headers->hasCacheControlDirective('immutable'));
        $this->assertNotNull($response->headers->get('Expires'));
    }

    public function test_serves_the_file_when_it_changed_since(): void
    {
        config(['app.debug' => false]);

        $this->get('/tallkit/tallkit.js', ['If-Modified-Since' => 'Mon, 01 Jan 2001 00:00:00 GMT'])->assertOk();
        $this->get('/tallkit/tallkit.js', ['If-Modified-Since' => 'not a date'])->assertOk();
    }
}

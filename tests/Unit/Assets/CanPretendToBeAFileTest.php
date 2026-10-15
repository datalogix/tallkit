<?php

namespace TALLKit\Tests\Unit\Assets;

use Illuminate\Http\Request;
use TALLKit\Assets\CanPretendToBeAFile;
use TALLKit\Tests\TestCase;

class CanPretendToBeAFileTest extends TestCase
{
    protected function subject(): object
    {
        return new class
        {
            use CanPretendToBeAFile;

            public function cacheMatches($lastModified): bool
            {
                return $this->matchesCache($lastModified);
            }
        };
    }

    public function test_content_type_is_guessed_only_for_javascript(): void
    {
        config(['app.debug' => true]);
        $file = tempnam(sys_get_temp_dir(), 'tk').'.css';
        file_put_contents($file, 'a{}');

        try {
            $response = $this->subject()->pretendResponseIsFile($file);
            $this->assertNull($response->headers->get('Content-Type'));

            $response = $this->subject()->pretendResponseIsFile($file, 'text/plain');
            $this->assertSame('text/plain', $response->headers->get('Content-Type'));
        } finally {
            @unlink($file);
        }
    }

    public function test_matches_cache_needs_a_known_modification_time(): void
    {
        $this->app->instance(Request::class, Request::create('/', server: ['HTTP_IF_MODIFIED_SINCE' => gmdate('D, d M Y H:i:s', 0).' GMT']));

        $this->assertFalse($this->subject()->cacheMatches(false));
        $this->assertTrue($this->subject()->cacheMatches(0));
        $this->assertFalse($this->subject()->cacheMatches(1));
    }

    public function test_matches_cache_needs_the_request_header(): void
    {
        $this->app->instance(Request::class, Request::create('/'));

        $this->assertFalse($this->subject()->cacheMatches(0));
    }
}

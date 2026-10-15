<?php

namespace TALLKit\Tests\Feature\Http;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Mockery;
use TALLKit\Tests\TestCase;

class UploadControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    protected function uploadUrl(array $params = ['guest' => 1]): string
    {
        return URL::temporarySignedRoute('tallkit.upload', now()->addHour(), $params, absolute: false);
    }

    protected function image(string $name = 'photo.png', int $kilobytes = 10): UploadedFile
    {
        return UploadedFile::fake()->image($name)->size($kilobytes);
    }

    public function test_stores_a_guest_upload(): void
    {
        $response = $this->postJson($this->uploadUrl(), ['file' => $this->image()])->assertOk();

        $path = $response->json('path');

        $this->assertStringStartsWith('tallkit-uploads/', $path);
        $this->assertStringEndsWith('.png', $path);
        $this->assertSame('public', $response->json('disk'));
        $this->assertSame(Storage::disk('public')->url($path), $response->json('url'));
        Storage::disk('public')->assertExists($path);
    }

    public function test_stores_an_authenticated_upload(): void
    {
        $this->actingAs(new User);

        $this->postJson($this->uploadUrl([]), ['file' => $this->image()])->assertOk()->assertJsonPath('disk', 'public');
    }

    public function test_uses_the_configured_guard(): void
    {
        config(['tallkit.upload.guard' => 'other', 'auth.guards.other' => ['driver' => 'session', 'provider' => 'users']]);

        $this->actingAs(new User);
        $this->postJson($this->uploadUrl([]), ['file' => $this->image()])->assertUnauthorized();

        $this->actingAs(new User, 'other');
        $this->postJson($this->uploadUrl([]), ['file' => $this->image()])->assertOk();
    }

    public function test_refuses_an_invalid_signature(): void
    {
        $this->postJson('/tallkit/upload?guest=1', ['file' => $this->image()])
            ->assertForbidden()
            ->assertJsonPath('message', 'The upload link is invalid or has expired. Reload the page and try again.');

        $this->postJson($this->uploadUrl().'&directory=other', ['file' => $this->image()])->assertForbidden();
    }

    public function test_refuses_an_expired_link(): void
    {
        $url = $this->uploadUrl();

        $this->travel(2)->hours();

        $this->postJson($url, ['file' => $this->image()])->assertForbidden();
    }

    public function test_refuses_guests_without_a_guest_link(): void
    {
        $this->postJson($this->uploadUrl([]), ['file' => $this->image()])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_refuses_several_files(): void
    {
        $this->postJson($this->uploadUrl(), ['file' => [$this->image('a.png'), $this->image('b.png')]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['file' => 'The file field must be a file.']);
    }

    public function test_refuses_several_files_with_the_translated_attribute(): void
    {
        trans('validation.file');
        app('translator')->addLines(['validation.attributes.file' => 'upload'], 'en');

        $this->postJson($this->uploadUrl(), ['file' => [$this->image()]])
            ->assertJsonValidationErrors(['file' => 'The upload field must be a file.']);
    }

    public function test_refuses_a_disk_not_allowed(): void
    {
        config(['filesystems.disks.other' => ['driver' => 'local', 'root' => storage_path('other')]]);
        Storage::fake('other');

        $this->postJson($this->uploadUrl(['guest' => 1, 'disk' => 'other']), ['file' => $this->image()])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['disk' => 'The selected disk is invalid.']);
    }

    public function test_stores_on_an_allowed_disk(): void
    {
        config(['filesystems.disks.other' => ['driver' => 'local', 'root' => storage_path('other')]]);
        Storage::fake('other');
        config(['tallkit.upload.allowed_disks' => ['public', 'other']]);

        $path = $this->postJson($this->uploadUrl(['guest' => 1, 'disk' => 'other']), ['file' => $this->image()])
            ->assertOk()
            ->assertJsonPath('disk', 'other')
            ->json('path');

        Storage::disk('other')->assertExists($path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_requires_a_file(): void
    {
        $this->postJson($this->uploadUrl(), [])->assertJsonValidationErrors('file');
    }

    public function test_refuses_extensions_not_allowed(): void
    {
        $this->postJson($this->uploadUrl(), ['file' => UploadedFile::fake()->create('page.html', 1, 'text/html')])
            ->assertJsonValidationErrors('file');
    }

    public function test_refuses_when_no_extension_is_allowed(): void
    {
        config(['tallkit.upload.allowed_extensions' => ['pdf']]);

        $this->postJson($this->uploadUrl(['guest' => 1, 'types' => 'image']), ['file' => $this->image()])
            ->assertJsonValidationErrors('file');
    }

    public function test_limits_extensions_to_the_signed_types(): void
    {
        $pdf = UploadedFile::fake()->create('doc.pdf', 5, 'application/pdf');

        $this->postJson($this->uploadUrl(['guest' => 1, 'types' => 'image,video']), ['file' => $pdf])
            ->assertJsonValidationErrors('file');

        $this->postJson($this->uploadUrl(['guest' => 1, 'types' => 'image,video']), ['file' => $this->image()])
            ->assertOk();

        $this->postJson($this->uploadUrl(['guest' => 1, 'types' => 'pdf']), ['file' => $pdf])
            ->assertOk();
    }

    public function test_limits_the_size_by_the_kind_ceiling(): void
    {
        $this->postJson($this->uploadUrl(), ['file' => $this->image(kilobytes: 5121)])->assertJsonValidationErrors('file');
        $this->postJson($this->uploadUrl(), ['file' => $this->image(kilobytes: 5120)])->assertOk();
    }

    public function test_limits_the_size_by_the_signed_max_size(): void
    {
        $this->postJson($this->uploadUrl(['guest' => 1, 'max_size' => 100]), ['file' => $this->image(kilobytes: 101)])
            ->assertJsonValidationErrors('file');

        $this->postJson($this->uploadUrl(['guest' => 1, 'max_size' => 100]), ['file' => $this->image(kilobytes: 100)])
            ->assertOk();
    }

    public function test_limits_the_size_by_the_signed_max_size_of_the_kind(): void
    {
        $url = $this->uploadUrl(['guest' => 1, 'max_size' => ['image' => 50, 'video' => 1000]]);

        $this->postJson($url, ['file' => $this->image(kilobytes: 51)])->assertJsonValidationErrors('file');
        $this->postJson($url, ['file' => $this->image(kilobytes: 50)])->assertOk();

        $pdf = UploadedFile::fake()->create('doc.pdf', 60, 'application/pdf');
        $this->postJson($url, ['file' => $pdf])->assertOk();
    }

    public function test_the_body_may_only_ask_for_a_smaller_size(): void
    {
        $url = $this->uploadUrl(['guest' => 1, 'max_size' => 100]);

        $this->postJson($url, ['file' => $this->image(kilobytes: 60), 'max_size' => 50])->assertJsonValidationErrors('file');
        $this->postJson($url, ['file' => $this->image(kilobytes: 150), 'max_size' => 1000])->assertJsonValidationErrors('file');
        $this->postJson($url, ['file' => $this->image(kilobytes: 90), 'max_size' => 'abc'])->assertOk();

        $this->postJson($this->uploadUrl(), ['file' => $this->image(kilobytes: 60), 'max_size' => 50])->assertJsonValidationErrors('file');
    }

    public function test_stores_in_a_sanitized_sub_directory(): void
    {
        $path = $this->postJson($this->uploadUrl(['guest' => 1, 'directory' => '../posts\\./images//']), ['file' => $this->image()])
            ->assertOk()
            ->json('path');

        $this->assertMatchesRegularExpression('#^tallkit-uploads/posts/images/[^/]+\.png$#', $path);
    }

    public function test_stores_without_a_base_directory(): void
    {
        config(['tallkit.upload.directory' => '/']);

        $this->assertMatchesRegularExpression('#^[^/]+\.png$#', $this->postJson($this->uploadUrl(), ['file' => $this->image()])->json('path'));

        $this->assertMatchesRegularExpression(
            '#^posts/[^/]+\.png$#',
            $this->postJson($this->uploadUrl(['guest' => 1, 'directory' => 'posts']), ['file' => $this->image()])->json('path'),
        );
    }

    public function test_reports_a_disk_that_throws(): void
    {
        Exceptions::fake();
        $disk = Mockery::mock(Filesystem::class);
        $disk->shouldReceive('putFileAs')->andThrow(new \RuntimeException('Disk down'));
        Storage::set('public', $disk);

        $this->postJson($this->uploadUrl(), ['file' => $this->image()])
            ->assertStatus(500)
            ->assertJsonPath('message', 'The file could not be saved.');

        Exceptions::assertReported(fn (\RuntimeException $e) => $e->getMessage() === 'Disk down');
    }

    public function test_reports_a_disk_that_does_not_save(): void
    {
        Exceptions::fake();
        $disk = Mockery::mock(Filesystem::class);
        $disk->shouldReceive('putFileAs')->andReturn(false);
        Storage::set('public', $disk);

        $this->postJson($this->uploadUrl(), ['file' => $this->image()])
            ->assertStatus(500)
            ->assertJsonPath('message', 'The file could not be saved.');

        Exceptions::assertReported(fn (\RuntimeException $e) => $e->getMessage() === 'TALLKit: an upload could not be saved on the [public] disk, in [tallkit-uploads].');
    }

    public function test_deletes_the_file_when_the_disk_has_no_url(): void
    {
        $disk = Mockery::mock(Filesystem::class);
        $disk->shouldReceive('putFileAs')->andReturn('tallkit-uploads/a.png');
        $disk->shouldReceive('url')->with('tallkit-uploads/a.png')->andThrow(new \RuntimeException('No URLs'));
        $disk->shouldReceive('delete')->once()->with('tallkit-uploads/a.png')->andReturn(true);
        Storage::set('public', $disk);

        $this->postJson($this->uploadUrl(), ['file' => $this->image()])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'The file was uploaded to a disk with no public address.');
    }

    public function test_deletes_the_file_when_the_url_is_blank(): void
    {
        $disk = Mockery::mock(Filesystem::class);
        $disk->shouldReceive('putFileAs')->andReturn('tallkit-uploads/a.png');
        $disk->shouldReceive('url')->andReturn('');
        $disk->shouldReceive('delete')->once()->with('tallkit-uploads/a.png')->andReturn(true);
        Storage::set('public', $disk);

        $this->postJson($this->uploadUrl(), ['file' => $this->image()])->assertUnprocessable();
    }
}

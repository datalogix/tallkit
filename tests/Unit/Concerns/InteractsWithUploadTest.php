<?php

namespace TALLKit\Tests\Unit\Concerns;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use TALLKit\Facades\TALLKit;
use TALLKit\Rules\Upload;
use TALLKit\Tests\TestCase;

class InteractsWithUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    protected function publicUrl(string $path = ''): string
    {
        return Storage::disk('public')->url($path);
    }

    public function test_uploads_in_use_callback(): void
    {
        $this->assertFalse(TALLKit::hasUploadsInUse());

        TALLKit::uploadsInUse(fn (array $paths, string $disk) => ['x' => $paths[0].'@'.$disk]);

        $this->assertTrue(TALLKit::hasUploadsInUse());
        $this->assertSame(['a.png@public'], TALLKit::uploadsInUseOf(['a.png'], 'public'));
    }

    public function test_upload_disk_and_directory(): void
    {
        $this->assertSame('public', TALLKit::uploadDisk());
        $this->assertSame('public', TALLKit::uploadDisk(''));
        $this->assertSame('s3', TALLKit::uploadDisk('s3'));

        config(['tallkit.upload.disk' => 'local', 'tallkit.upload.directory' => '/files/']);

        $this->assertSame('local', TALLKit::uploadDisk());
        $this->assertSame('files', TALLKit::uploadDirectory());
    }

    public function test_editor_upload_paths(): void
    {
        $base = $this->publicUrl();
        $html = '<img src="'.$base.'tallkit-uploads/a.png">'
            .'<a href=\'/storage/tallkit-uploads/b.pdf\'>b</a>'
            .'<video poster="tallkit-uploads/c.jpg"></video>'
            .'<img src="'.$base.'tallkit-uploads/a.png">'
            .'<img src="'.$base.'other/d.png">'
            .'<img src="'.$base.'tallkit-uploads/../secret.txt">'
            .'<img src="tallkit-uploads/x/./y.png">'
            .'<img src="'.$base.'tallkit-uploads/e&amp;f.png">';

        $paths = TALLKit::editorUploadPaths($html);

        $this->assertInstanceOf(Collection::class, $paths);
        $this->assertSame([
            'tallkit-uploads/a.png',
            'tallkit-uploads/b.pdf',
            'tallkit-uploads/c.jpg',
            'tallkit-uploads/e&f.png',
        ], $paths->all());

        $this->assertSame([], TALLKit::editorUploadPaths(null)->all());
    }

    public function test_editor_upload_paths_with_unconfigured_disk(): void
    {
        $this->assertSame(
            ['tallkit-uploads/a.png'],
            TALLKit::editorUploadPaths('<img src="/tallkit-uploads/a.png">', 'missing')->all(),
        );
    }

    public function test_editor_upload_disabled(): void
    {
        $this->assertNull(TALLKit::editorUpload(false));
    }

    public function test_editor_upload_defaults(): void
    {
        $upload = TALLKit::editorUpload();

        $this->assertSame(['image' => 5120, 'video' => 51200], $upload['maxSize']);
        $this->assertStringStartsWith('/tallkit/upload?', $upload['url']);

        parse_str(parse_url($upload['url'], PHP_URL_QUERY), $query);

        $this->assertSame('image,video', $query['types']);
        $this->assertSame(['image' => '5120', 'video' => '51200'], $query['max_size']);
        $this->assertArrayHasKey('signature', $query);
        $this->assertArrayHasKey('expires', $query);
        $this->assertArrayNotHasKey('guest', $query);
        $this->assertArrayNotHasKey('disk', $query);
        $this->assertEqualsWithDelta(now()->addMinutes(60 * 24)->timestamp, (int) $query['expires'], 5);
    }

    public function test_editor_upload_with_true(): void
    {
        $this->assertSame(['image' => 5120, 'video' => 51200], TALLKit::editorUpload(true)['maxSize']);
    }

    public function test_editor_upload_for_guest(): void
    {
        $upload = TALLKit::editorUpload(['guest' => true, 'disk' => 'public', 'directory' => 'posts']);

        $this->assertSame(['image' => 2048], $upload['maxSize']);

        parse_str(parse_url($upload['url'], PHP_URL_QUERY), $query);

        $this->assertSame('1', $query['guest']);
        $this->assertSame('public', $query['disk']);
        $this->assertSame('posts', $query['directory']);
        $this->assertSame('image', $query['types']);
        $this->assertEqualsWithDelta(now()->addMinutes(120)->timestamp, (int) $query['expires'], 5);
    }

    public function test_editor_upload_guest_without_allowed_types(): void
    {
        $this->assertNull(TALLKit::editorUpload(['guest' => true, 'types' => ['video']]));
    }

    public function test_editor_upload_guest_types_null_keeps_types(): void
    {
        config(['tallkit.upload.guest_types' => null, 'tallkit.upload.guest_max_size' => null]);

        $upload = TALLKit::editorUpload(['guest' => true, 'types' => ['pdf']]);

        $this->assertSame(['pdf' => 20480], $upload['maxSize']);
    }

    public function test_editor_upload_max_size_scalar_and_per_type(): void
    {
        $this->assertSame(
            ['image' => 100, 'video' => 100],
            TALLKit::editorUpload(['maxSize' => 100])['maxSize'],
        );

        $this->assertSame(
            ['image' => 300, 'video' => 51200],
            TALLKit::editorUpload(['maxSize' => ['image' => 300]])['maxSize'],
        );

        $this->assertSame(
            ['image' => 5120],
            TALLKit::editorUpload(['types' => ['image'], 'maxSize' => 99999])['maxSize'],
        );
    }

    public function test_editor_upload_with_given_url(): void
    {
        $this->assertSame(
            ['url' => '/my-upload', 'maxSize' => ['image' => 5120]],
            TALLKit::editorUpload(['url' => '/my-upload', 'types' => ['image'], 'disk' => 'other']),
        );
    }

    public function test_editor_upload_without_route(): void
    {
        app('router')->setRoutes(new RouteCollection);

        $this->assertNull(TALLKit::editorUpload());
    }

    public function test_editor_upload_with_disallowed_disk(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The disk [local] isn\'t configured, or isn\'t in tallkit.upload.allowed_disks.');

        TALLKit::editorUpload(['disk' => 'local']);
    }

    public function test_editor_upload_with_unknown_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The upload type [foo] doesn\'t exist. Use one of: image, video');

        TALLKit::editorUpload(['types' => ['foo']]);
    }

    public function test_editor_upload_messages(): void
    {
        $this->assertSame([
            'tooLarge' => 'The file may not be larger than :size.',
            'invalidType' => 'This file type is not allowed.',
            'failed' => 'The file could not be uploaded.',
        ], TALLKit::editorUploadMessages());
    }

    public function test_editor_translations(): void
    {
        $quill = TALLKit::editorTranslations('quill');

        $this->assertSame('Strikethrough', $quill['buttons']['strike']);
        $this->assertSame('Enter link:', $quill['texts']['enterLink']);

        $editorjs = TALLKit::editorTranslations('editorjs');

        $this->assertSame('Click to tune', $editorjs['ui']['blockTunes']['toggler']['Click to tune']);
        $this->assertSame('Inline code', $editorjs['toolNames']['InlineCode']);
        $this->assertSame('Add a link', $editorjs['tools']['link']['Add a link']);
        $this->assertSame('Move down', $editorjs['blockTunes']['moveDown']['Move down']);

        $this->assertSame([], TALLKit::editorTranslations('tiptap'));
    }

    public function test_uploaded_files_from_stored_paths(): void
    {
        Storage::disk('public')->put('docs/report.pdf', 'pdf-content');

        $files = TALLKit::uploadedFiles(['docs/report.pdf', 'docs/missing.pdf', 123]);

        $this->assertSame([[
            'name' => 'report.pdf',
            'url' => $this->publicUrl('docs/report.pdf'),
            'size' => 11,
            'type' => 'pdf',
            'status' => 'done',
            'progress' => 100,
            'tmpFilename' => null,
            'value' => 'docs/report.pdf',
        ]], $files->all());
    }

    public function test_uploaded_files_from_full_url_and_request_memo(): void
    {
        Storage::disk('public')->put('a.png', 'x');

        $url = $this->publicUrl('a.png');

        $first = TALLKit::uploadedFiles($url)->first();

        $this->assertSame($url, $first['value']);
        $this->assertSame('a.png', $first['name']);

        Storage::disk('public')->delete('a.png');

        $this->assertSame('a.png', TALLKit::uploadedFiles('a.png')->first()['name']);
    }

    public function test_uploaded_files_only_stored(): void
    {
        Storage::disk('public')->put('a.png', 'x');
        Storage::disk('public')->put('b.png', 'x');

        $files = TALLKit::uploadedFiles(['a.png', 'b.png'], stored: ['a.png']);

        $this->assertSame(['a.png'], $files->pluck('value')->all());
    }

    public function test_uploaded_files_on_remote_disk_are_cached(): void
    {
        Storage::fake('remote');
        config(['filesystems.disks.remote.driver' => 's3']);
        Storage::disk('remote')->put('a.png', 'abc');

        $file = TALLKit::uploadedFiles('a.png', 'remote')->first();

        $this->assertSame(3, $file['size']);
        $this->assertSame(
            $file['name'],
            Cache::get(TALLKit::storageKey('upload', md5('remote|a.png')))['name'],
        );

        $this->assertSame([], TALLKit::uploadedFiles('nope.png', 'remote')->all());
        $this->assertFalse(Cache::get(TALLKit::storageKey('upload', md5('remote|nope.png'))));
    }

    public function test_uploaded_files_on_unconfigured_disk(): void
    {
        $this->assertSame([], TALLKit::uploadedFiles('a.png', 'missing')->all());
    }

    public function test_uploaded_files_from_temporary_uploads(): void
    {
        Storage::fake('tmp-for-tests');

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        $image = 'hash1-meta'.base64_encode('photo.png').'-.png';
        $archive = 'hash2-meta'.base64_encode('files.zip').'-.zip';

        Storage::disk('tmp-for-tests')->put('livewire-tmp/'.$image, $png);
        Storage::disk('tmp-for-tests')->put('livewire-tmp/'.$archive, 'zip-content');

        $files = TALLKit::uploadedFiles([
            new TemporaryUploadedFile($image, 'tmp-for-tests'),
            new TemporaryUploadedFile($archive, 'tmp-for-tests'),
        ]);

        $this->assertSame('photo.png', $files[0]['name']);
        $this->assertStringContainsString('preview-file', $files[0]['url']);
        $this->assertSame(strlen($png), $files[0]['size']);
        $this->assertSame('image', $files[0]['type']);
        $this->assertSame('done', $files[0]['status']);
        $this->assertSame(100, $files[0]['progress']);
        $this->assertNull($files[0]['value']);
        $this->assertSame($image, $files[0]['tmpFilename']);

        $this->assertSame('files.zip', $files[1]['name']);
        $this->assertNull($files[1]['url']);
        $this->assertSame('archive', $files[1]['type']);
    }

    public function test_upload_server_value_without_session_or_id(): void
    {
        $this->assertSame('a.png', TALLKit::uploadServerValue(new \stdClass, 'file', 'a.png'));
        $this->assertSame('a.png', TALLKit::uploadServerValue($this->livewireLike('c1'), 'file', 'a.png'));
    }

    public function test_upload_server_value_trusts_initial_render(): void
    {
        $this->withRequest([]);

        $this->assertSame(['a.png', ''], TALLKit::uploadServerValue($this->livewireLike('c1'), 'files', ['a.png', '']));
        $this->assertSame(
            ['c1|files' => ['a.png']],
            session(TALLKit::storageKey('upload-paths')),
        );
    }

    public function test_upload_server_value_filters_untrusted_paths(): void
    {
        $this->withRequest(['components' => [
            'not-an-array',
            ['snapshot' => json_encode(['memo' => ['id' => 'other'], 'data' => []])],
            [
                'snapshot' => json_encode(['memo' => ['id' => 'c1'], 'data' => ['files' => ['old.png', 'known.png']]]),
                'updates' => ['files.0' => 'sent.png', 'other' => 'x.png'],
            ],
        ]]);

        session([TALLKit::storageKey('upload-paths') => ['c1|files' => ['known.png']]]);

        $value = TALLKit::uploadServerValue(
            $this->livewireLike('c1'),
            'files',
            ['known.png', 'old.png', 'sent.png', 'new.png', 7],
        );

        $this->assertSame(['known.png', 'new.png', 7], $value);
        $this->assertSame(['c1|files' => ['known.png', 'new.png']], session(TALLKit::storageKey('upload-paths')));
    }

    public function test_upload_server_value_with_scalar_and_traversable(): void
    {
        $this->withRequest(['components' => [[
            'snapshot' => json_encode(['memo' => ['id' => 'c1'], 'data' => ['form' => ['file' => 'old.png']]]),
            'updates' => ['form' => ['file' => 'sent.png']],
        ]]]);

        $component = $this->livewireLike('c1');

        $this->assertNull(TALLKit::uploadServerValue($component, 'form.file', 'old.png'));
        $this->assertNull(TALLKit::uploadServerValue($component, 'form.file', 'sent.png'));
        $this->assertSame('new.png', TALLKit::uploadServerValue($component, 'form.file', 'new.png'));
        $this->assertSame(5, TALLKit::uploadServerValue($component, 'form.file', 5));
        $this->assertSame(
            ['new.png'],
            TALLKit::uploadServerValue($component, 'form.file', collect(['old.png', 'new.png'])),
        );
        $this->assertSame(
            ['new.png'],
            TALLKit::uploadServerValue($component, 'form.file', new \ArrayIterator(['old.png', 'new.png'])),
        );
    }

    public function test_upload_server_value_trusts_traversable_paths_without_snapshot(): void
    {
        $this->withRequest([]);

        TALLKit::uploadServerValue($this->livewireLike('c1'), 'files', new \ArrayIterator(['a.png', 'b.png']));

        $this->assertSame(['c1|files' => ['a.png', 'b.png']], session(TALLKit::storageKey('upload-paths')));
    }

    public function test_upload_server_value_keeps_last_fifty_entries(): void
    {
        $this->withRequest([]);

        session([TALLKit::storageKey('upload-paths') => collect(range(1, 60))
            ->mapWithKeys(fn ($i) => ["c{$i}|f" => []])
            ->all()]);

        TALLKit::uploadServerValue($this->livewireLike('c1'), 'f', 'a.png');

        $shown = session(TALLKit::storageKey('upload-paths'));

        $this->assertCount(50, $shown);
        $this->assertSame('c1|f', array_key_last($shown));
        $this->assertArrayNotHasKey('c11|f', $shown);
        $this->assertArrayHasKey('c12|f', $shown);
    }

    public function test_upload_prune(): void
    {
        Storage::disk('public')->put('a.png', 'x');
        Storage::disk('public')->put('b.png', 'x');
        Cache::put(TALLKit::storageKey('upload', md5('public|a.png')), ['cached']);

        $removed = TALLKit::uploadPrune(['a.png', 'b.png', 'gone.png', ''], [$this->publicUrl('b.png')]);

        $this->assertSame(['a.png'], $removed);
        Storage::disk('public')->assertMissing('a.png');
        Storage::disk('public')->assertExists('b.png');
        $this->assertNull(Cache::get(TALLKit::storageKey('upload', md5('public|a.png'))));

        $this->assertSame([], TALLKit::uploadPrune(['b.png'], ['b.png']));
    }

    public function test_upload_prune_with_unconfigured_disk(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The disk [missing] is not configured.');

        TALLKit::uploadPrune([], [], 'missing');
    }

    public function test_upload_kept(): void
    {
        $file = UploadedFile::fake()->create('a.pdf');

        $this->assertSame(
            ['a.png', $file],
            TALLKit::uploadKept(['a.png'], ['a.png', 'forged.png', null, '', $file]),
        );
    }

    public function test_upload_is_stored(): void
    {
        $this->assertFalse(TALLKit::uploadIsStored('', ['a.png']));
        $this->assertFalse(TALLKit::uploadIsStored('a.png', null));
        $this->assertFalse(TALLKit::uploadIsStored('a.png', []));
        $this->assertTrue(TALLKit::uploadIsStored('a.png', 'a.png'));
        $this->assertTrue(TALLKit::uploadIsStored($this->publicUrl('a.png'), ['a.png']));
        $this->assertTrue(TALLKit::uploadIsStored('a.png', [$this->publicUrl('a.png')], 'public'));
        $this->assertFalse(TALLKit::uploadIsStored('b.png', ['a.png']));
    }

    public function test_upload_file_types_merge_config(): void
    {
        $this->assertSame(['jpg', 'jpeg', 'png', 'gif', 'webp'], TALLKit::uploadFileTypes()['image']);

        config(['tallkit.upload.file_types' => ['image' => ['HEIC', 'png'], 'model' => 'stl']]);

        $types = TALLKit::uploadFileTypes();

        $this->assertSame(['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic'], $types['image']);
        $this->assertSame(['stl'], $types['model']);
    }

    public function test_upload_file_type(): void
    {
        $this->assertSame('image', TALLKit::uploadFileType('PNG'));
        $this->assertSame('code', TALLKit::uploadFileType('json'));
        $this->assertSame('unknown', TALLKit::uploadFileType('exe'));
        $this->assertSame('unknown', TALLKit::uploadFileType(null));
    }

    public function test_uploaded_file_type(): void
    {
        $this->assertSame('default', TALLKit::uploadedFileType(null));
        $this->assertSame('image', TALLKit::uploadedFileType(UploadedFile::fake()->image('a.png')));
        $this->assertSame('video', TALLKit::uploadedFileType(UploadedFile::fake()->create('a.bin', 1, 'video/mp4')));
        $this->assertSame('audio', TALLKit::uploadedFileType(UploadedFile::fake()->create('a.bin', 1, 'audio/mpeg')));
        $this->assertSame('pdf', TALLKit::uploadedFileType(UploadedFile::fake()->create('a.pdf', 1, 'application/pdf')));

        $invalid = new UploadedFile(__FILE__, 'clip.MOV', null, UPLOAD_ERR_NO_FILE, true);

        $this->assertSame('video', TALLKit::uploadedFileType($invalid));
    }

    public function test_upload_allowed_extensions(): void
    {
        $this->assertContains('pdf', TALLKit::uploadAllowedExtensions());
        $this->assertSame(['mp4', 'mov', 'webm'], TALLKit::uploadAllowedExtensions('video'));

        config(['tallkit.upload.allowed_extensions' => []]);

        $all = TALLKit::uploadAllowedExtensions();

        $this->assertContains('md', $all);
        $this->assertNotContains('html', $all);
        $this->assertSame([], TALLKit::uploadAllowedExtensions('code'));
    }

    public function test_upload_max_size(): void
    {
        $this->assertSame(5120, TALLKit::uploadMaxSize('image'));
        $this->assertSame(20480, TALLKit::uploadMaxSize('pdf'));
        $this->assertSame(20480, TALLKit::uploadMaxSize());
        $this->assertSame(100, TALLKit::uploadMaxSize('image', 100));
        $this->assertSame(5120, TALLKit::uploadMaxSize('image', 99999));
        $this->assertSame(5120, TALLKit::uploadMaxSize('image', 0));

        config(['tallkit.upload.max_size' => 1000]);
        $this->assertSame(1000, TALLKit::uploadMaxSize('video'));

        config(['tallkit.upload.max_size' => ['image' => 10]]);
        $this->assertSame(20480, TALLKit::uploadMaxSize('pdf'));
    }

    public function test_upload_hint_text(): void
    {
        $this->assertSame(
            'Up to 3 files · max 5 KB each · Images',
            TALLKit::uploadHintText('image/*', 5, 3),
        );

        $this->assertSame(
            'max 5 MB · Images',
            TALLKit::uploadHintText('image/*', null, null, false),
        );

        $this->assertSame(
            'PNG, Videos, Audio, PDF, BIN, X-UNKNOWN',
            TALLKit::uploadHintText('.png, video/*, audio/*, application/pdf, application/octet-stream, foo/x-unknown, junk', null, null),
        );

        $this->assertSame('max 1 KB each', TALLKit::uploadHintText(null, 1, null));
        $this->assertNull(TALLKit::uploadHintText(null, null, null));
    }

    public function test_upload_disk_allowed(): void
    {
        $this->assertTrue(TALLKit::uploadDiskAllowed('public'));
        $this->assertFalse(TALLKit::uploadDiskAllowed('local'));

        config(['tallkit.upload.allowed_disks' => ['public', 'local', 'missing']]);

        $this->assertTrue(TALLKit::uploadDiskAllowed('local'));
        $this->assertFalse(TALLKit::uploadDiskAllowed('missing'));
    }

    public function test_upload_rules_single(): void
    {
        $rules = TALLKit::uploadRules('photo', 'image', 100, stored: ['a.png'], disk: 'public');

        $this->assertSame(['photo'], array_keys($rules));
        $this->assertCount(1, $rules['photo']);
        $this->assertInstanceOf(Upload::class, $rules['photo'][0]);

        $rule = (fn () => [$this->rules, $this->stored, $this->disk, $this->type, $this->maxSize])->call($rules['photo'][0]);

        $this->assertSame([['file', 'mimes:jpg,jpeg,png,gif,webp'], ['a.png'], 'public', 'image', 100], $rule);
    }

    public function test_upload_rules_multiple(): void
    {
        $rules = TALLKit::uploadRules('docs', multiple: true, maxFiles: 5);

        $this->assertSame(['array', 'max:5'], $rules['docs']);
        $this->assertInstanceOf(Upload::class, $rules['docs.*'][0]);

        $this->assertSame(['array'], TALLKit::uploadRules('docs', multiple: true)['docs']);
    }

    public function test_upload_rules_with_unknown_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The upload type [foo] doesn\'t exist.');

        TALLKit::uploadRules('file', 'foo');
    }

    public function test_upload_rules_without_allowed_extensions(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No extension of the upload type [code] is in tallkit.upload.allowed_extensions.');

        TALLKit::uploadRules('file', 'code');
    }

    protected function livewireLike(string $id): object
    {
        return new class($id)
        {
            public function __construct(protected string $id) {}

            public function getId(): string
            {
                return $this->id;
            }
        };
    }

    protected function withRequest(array $input): void
    {
        $request = Request::create('/livewire/update', 'POST', $input);
        $request->setLaravelSession(app('session.store'));

        $this->app->instance('request', $request);
    }
}

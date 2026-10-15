<?php

namespace TALLKit\Tests\Unit\Rules;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use TALLKit\Rules\Upload;
use TALLKit\Tests\TestCase;

class UploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    protected function rule(mixed $stored = null, ?string $type = null, ?int $maxSize = null, ?string $disk = null): Upload
    {
        return new Upload(['file', 'mimes:jpg,png,pdf'], $stored, $disk, $type, $maxSize);
    }

    protected function errors(array $data, array $rules, array $messages = [], array $attributes = []): array
    {
        return Validator::make($data, $rules, $messages, $attributes)->errors()->toArray();
    }

    public function test_passes_a_valid_uploaded_file(): void
    {
        $this->assertSame([], $this->errors(['photo' => UploadedFile::fake()->image('a.png')], ['photo' => [$this->rule()]]));
    }

    public function test_fails_with_the_inner_rules_message(): void
    {
        $file = UploadedFile::fake()->create('a.txt', 1, 'text/plain');

        $this->assertSame(
            ['photo' => ['The photo field must be a file of type: jpg, png, pdf.']],
            $this->errors(['photo' => $file], ['photo' => [$this->rule()]]),
        );
    }

    public function test_limits_the_size_by_the_detected_kind(): void
    {
        $this->assertSame(
            ['photo' => ['The photo field must not be greater than 5120 kilobytes.']],
            $this->errors(['photo' => UploadedFile::fake()->image('a.png')->size(5121)], ['photo' => [$this->rule()]]),
        );

        $this->assertSame([], $this->errors(['doc' => UploadedFile::fake()->create('a.pdf', 6000, 'application/pdf')], ['doc' => [$this->rule()]]));
    }

    public function test_limits_the_size_by_the_given_type_and_max_size(): void
    {
        $pdf = UploadedFile::fake()->create('a.pdf', 6000, 'application/pdf');

        $this->assertArrayHasKey('doc', $this->errors(['doc' => $pdf], ['doc' => [$this->rule(type: 'image')]]));

        $this->assertSame(
            ['doc' => ['The doc field must not be greater than 100 kilobytes.']],
            $this->errors(['doc' => UploadedFile::fake()->create('b.pdf', 101, 'application/pdf')], ['doc' => [$this->rule(maxSize: 100)]]),
        );
    }

    public function test_fails_a_value_that_is_not_a_file(): void
    {
        foreach ([123, ['a']] as $value) {
            $this->assertSame(
                ['photo' => ['The photo field must be a file.']],
                $this->errors(['photo' => $value], ['photo' => [$this->rule()]]),
            );
        }
    }

    public function test_passes_a_path_that_is_already_stored(): void
    {
        Storage::disk('public')->put('tallkit-uploads/a.png', 'x');

        $this->assertSame([], $this->errors(['photo' => 'tallkit-uploads/a.png'], ['photo' => [$this->rule('tallkit-uploads/a.png')]]));
        $this->assertSame([], $this->errors(['photo' => 'tallkit-uploads/a.png'], ['photo' => [$this->rule(['tallkit-uploads/a.png'], disk: 'public')]]));
    }

    public function test_fails_a_path_that_is_not_stored(): void
    {
        $this->assertSame(
            ['photo' => ['The photo field must be a file.']],
            $this->errors(['photo' => '../../.env'], ['photo' => [$this->rule('tallkit-uploads/a.png')]]),
        );

        $this->assertSame(
            ['photo' => ['The photo field must be a file.']],
            $this->errors(['photo' => 'tallkit-uploads/a.png'], ['photo' => [$this->rule()]]),
        );
    }

    public function test_names_array_items_by_their_field(): void
    {
        $data = ['photos' => ['x'], 'post' => ['cover_images' => [1]]];
        $rules = ['photos.*' => [$this->rule()], 'post.cover_images.*' => [$this->rule()]];

        $this->assertSame([
            'photos.0' => ['The photos field must be a file.'],
            'post.cover_images.0' => ['The cover images field must be a file.'],
        ], $this->errors($data, $rules));
    }

    public function test_names_array_items_of_uploaded_files_by_their_field(): void
    {
        $file = UploadedFile::fake()->create('a.txt', 1, 'text/plain');

        $this->assertSame(
            ['photos.0' => ['The photos field must be a file of type: jpg, png, pdf.']],
            $this->errors(['photos' => [$file]], ['photos.*' => [$this->rule()]]),
        );
    }

    public function test_uses_custom_attribute_names(): void
    {
        $this->assertSame(
            ['photos.0' => ['The gallery picture field must be a file.']],
            $this->errors(['photos' => ['x']], ['photos.*' => [$this->rule()]], attributes: ['photos.*' => 'gallery picture']),
        );

        $this->assertSame(
            ['user_photo' => ['The avatar field must be a file.']],
            $this->errors(['user_photo' => 1], ['user_photo' => [$this->rule()]], attributes: ['user_photo' => 'avatar']),
        );

        $this->assertSame(
            ['userPhoto' => ['The user photo field must be a file.']],
            $this->errors(['userPhoto' => 1], ['userPhoto' => [$this->rule()]]),
        );
    }

    public function test_uses_the_custom_messages_of_the_validator(): void
    {
        $file = UploadedFile::fake()->image('a.png')->size(6000);

        $this->assertSame(
            ['photo' => ['Too big: photo.']],
            $this->errors(['photo' => $file], ['photo' => [$this->rule()]], ['photo.max' => 'Too big: :attribute.']),
        );
    }

    public function test_works_without_a_validator(): void
    {
        $messages = [];

        $this->rule()->validate('post.photos.0', 'x', function ($message) use (&$messages) {
            $messages[] = $message;
        });

        $this->rule()->validate('photo', '', function ($message) use (&$messages) {
            $messages[] = $message;
        });

        $this->rule()->validate('photo', UploadedFile::fake()->image('a.png'), function ($message) use (&$messages) {
            $messages[] = $message;
        });

        $this->assertSame(['The photos field must be a file.', 'The photo field must be a file.'], $messages);
    }

    public function test_set_validator_returns_the_rule(): void
    {
        $rule = $this->rule();

        $this->assertSame($rule, $rule->setValidator(Validator::make([], [])));
    }
}

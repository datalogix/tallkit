<?php

namespace TALLKit\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator as ValidationValidator;
use TALLKit\Facades\TALLKit;

class Upload implements ValidationRule, ValidatorAwareRule
{
    protected ?ValidationValidator $validator = null;

    // $stored: the value saved before, from the model, never from the browser.
    public function __construct(
        protected array $rules,
        protected mixed $stored = null,
        protected ?string $disk = null,
        protected ?string $type = null,
        protected ?int $maxSize = null,
    ) {}

    public function setValidator($validator): static
    {
        $this->validator = $validator;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && $value !== '') {
            if (! TALLKit::uploadIsStored($value, $this->stored, $this->disk)) {
                $fail(__('validation.file', ['attribute' => $this->attributeName($attribute)]));
            }

            return;
        }

        if (! $value instanceof UploadedFile) {
            $fail(__('validation.file', ['attribute' => $this->attributeName($attribute)]));

            return;
        }

        $maxSize = TALLKit::uploadMaxSize($this->type ?? TALLKit::uploadedFileType($value), $this->maxSize);

        // A plain key: a dotted one would be read as a path into the data.
        $check = Validator::make(['file' => $value], ['file' => [...$this->rules, "max:{$maxSize}"]], [], [
            'file' => $this->attributeName($attribute),
        ]);

        if ($check->fails()) {
            $fail($check->errors()->first('file'));
        }
    }

    protected function attributeName(string $attribute): string
    {
        $plain = str_replace('_', ' ', Str::snake($attribute));
        $named = $this->validator?->getDisplayableAttribute($attribute) ?? $plain;

        if (! in_array($named, [$attribute, $plain, str_replace('_', ' ', $attribute)], true) || ! preg_match('/\.\d+(\.|$)/', $attribute)) {
            return $named;
        }

        $parts = array_filter(explode('.', $attribute), fn ($part) => ! ctype_digit($part));

        return str_replace('_', ' ', Str::snake((string) end($parts)));
    }
}

<?php

namespace TALLKit\Components\Input;

use Illuminate\Support\Str;
use Illuminate\View\ComponentSlot;
use TALLKit\Attributes\Mount;
use TALLKit\Concerns\InteractsWithField;
use TALLKit\View\BladeComponent;

class Input extends BladeComponent
{
    use InteractsWithField;

    public function __construct(
        public ?string $type = null,
        public bool|string|null $mask = null,
        public string|ComponentSlot|null $icon = null,
        public string|ComponentSlot|null $iconTrailing = null,
        public string|ComponentSlot|null $kbd = null,
        public bool|string|null $loading = null,
        public ?bool $clearable = null,
        public ?bool $copyable = null,
        public ?bool $viewable = null,
    ) {}

    #[Mount()]
    protected function mountType()
    {
        $this->type ??= $this->detectType();
        $this->mask = $this->detectMask();
        $this->viewable ??= $this->type === 'password';
    }

    #[Mount()]
    protected function mountLoading()
    {
        $this->setVariables('wireTarget');
        $this->wireTarget = null;

        if (! (is_string($this->loading) || $this->loading === null)) {
            return;
        }

        $wireModel = $this->attributes->wire('model');

        if ($wireModel?->directive) {
            $this->loading = $wireModel->hasModifier('live');
            $this->wireTarget = $this->loading ? $wireModel->value() : null;

            return;
        }

        $this->wireTarget = $this->loading;
        $this->loading = (bool) $this->loading;
    }

    protected function detectType()
    {
        if (blank($this->name)) {
            return 'text';
        }

        $types = [
            'color' => ['color'],
            'date' => ['date', 'birthdate', 'birth_date', '_at'],
            'datetime-local' => ['datetime', 'date_time'],
            'email' => ['email'],
            'file' => ['image', 'picture', 'photo', 'logo', 'background', 'audio', 'video', 'file', 'document'],
            'password' => ['password', 'password_confirmation', 'new_password', 'new_password_confirmation'],
            'url' => ['url', 'website', 'youtube', 'vimeo', 'facebook', 'twitter', 'instagram', 'linkedin'],
            'time' => ['time'],
            'tel' => ['phone', 'whatsapp'],
        ];

        foreach ($types as $type => $names) {
            if (Str::contains($this->name, $names, true)) {
                return $type;
            }
        }

        return 'text';
    }

    protected function detectMask()
    {
        if ($this->mask === false || $this->type !== 'text') {
            return null;
        }

        $masks = [
            '99999-999' => ['cep', 'zipcode', 'zip-code'],
            '99/99/9999' => ['date', 'birthdate', 'birth_date', '_at'],
            '99/99/9999 99:99' => ['datetime', 'date_time'],
            '99:99' => ['time'],
            '999.999.999-99' => ['cpf'],
            '99.999.999/9999-99' => ['cnpj'],
            '(99) 999999999' => ['tel', 'phone', 'whatsapp'],
        ];

        if (is_string($this->mask)) {
            foreach ($masks as $maskValue => $names) {
                if (Str::contains($this->mask, $names, true)) {
                    return $maskValue;
                }
            }

            return $this->mask;
        }

        if (blank($this->name) && blank($this->type)) {
            return null;
        }

        foreach ($masks as $maskValue => $names) {
            if (Str::contains($this->name, $names, true) || Str::contains($this->type, $names, true)) {
                return $maskValue;
            }
        }

        return null;
    }

    public function paddingEnd(bool $appendLoadingToCount = false)
    {
        $countOfTrailingIcons = collect([
            $appendLoadingToCount,
            (bool) $this->iconTrailing,
            (bool) $this->kbd,
            (bool) $this->clearable,
            (bool) $this->copyable,
            (bool) $this->viewable,
        ])->filter()->count();

        return $countOfTrailingIcons > 0
            ? 'padding-inline-end: '.match ($this->size) {
                'xs' => 32 * $countOfTrailingIcons.'px;',
                'sm' => 36 * $countOfTrailingIcons.'px;',
                default => 40 * $countOfTrailingIcons.'px;',
                'lg' => 48 * $countOfTrailingIcons.'px;',
                'xl' => 56 * $countOfTrailingIcons.'px;',
                '2xl' => 64 * $countOfTrailingIcons.'px;',
                '3xl' => 72 * $countOfTrailingIcons.'px;',
            } : '';
    }
}

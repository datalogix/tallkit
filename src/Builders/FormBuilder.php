<?php

namespace TALLKit\Builders;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Fluent;
use Illuminate\View\ComponentAttributeBag;
use Stringable;

class FormBuilder extends Fluent implements Htmlable, Stringable
{
    protected $submit = null;

    protected $elements = [];

    public function add(Element $element)
    {
        $this->elements[] = $element;

        return $this;
    }

    public function group()
    {
        $group = Group::make();

        foreach (func_get_args() as $element) {
            $group->add($element);
        }

        return $this->add($group);
    }

    public function input(
        ?string $name = null,
        ?string $label = null,
        null|bool|string $placeholder = null,
        ?string $value = null,
        ?bool $required = null,
        ?string $help = null,
        ?string $information = null,
        ?string $icon = null,
        ?array $attributes = null,
        ?string $type = null,
    ) {
        return $this->add(Input::make(get_defined_vars()));
    }

    public function url(...$args)
    {
        return $this->input(...[...$args, 'type' => 'url']);
    }

    public function email(...$args)
    {
        return $this->input(...[...$args, 'type' => 'email']);
    }

    public function password(...$args)
    {
        return $this->input(...[...$args, 'type' => 'password']);
    }

    /*public function date(...$args) {
        return $this->input(...[...$args, 'type' => 'date']);
    }*/

    public function datetime(...$args)
    {
        return $this->input(...[...$args, 'type' => 'datetime-local']);
    }

    public function time(...$args)
    {
        return $this->input(...[...$args, 'type' => 'time']);
    }

    public function color(...$args)
    {
        return $this->input(...[...$args, 'type' => 'color']);
    }

    public function toggle(
        ?string $name = null,
        ?string $label = null,
        ?string $value = null,
        ?bool $required = null,
        ?string $help = null,
        ?string $information = null,
        ?array $attributes = null,
    ) {
        return $this->add(Toggle::make(get_defined_vars()));
    }

    public function checkbox(
        ?string $name = null,
        ?string $label = null,
        ?string $value = null,
        ?bool $required = null,
        ?string $help = null,
        ?string $information = null,
        ?array $attributes = null,
    ) {
        return $this->add(Checkbox::make(get_defined_vars()));
    }

    public function radio(
        ?string $name = null,
        ?string $label = null,
        ?string $value = null,
        ?bool $required = null,
        ?string $help = null,
        ?string $information = null,
        ?array $attributes = null,
    ) {
        return $this->add(Radio::make(get_defined_vars()));
    }

    public function textarea(
        ?string $name = null,
        ?string $label = null,
        ?string $value = null,
        ?bool $required = null,
        null|string|int $rows = null,
        ?string $help = null,
        ?string $information = null,
        ?array $attributes = null,
    ) {
        return $this->add(Textarea::make(get_defined_vars()));
    }

    public function select(
        ?string $name = null,
        ?string $label = null,
        mixed $options = null,
        ?string $value = null,
        ?bool $required = null,
        ?bool $multiple = null,
        ?int $rows = null,
        ?string $help = null,
        ?string $information = null,
        ?array $attributes = null,
    ) {
        return $this->add(Select::make(get_defined_vars()));
    }

    public function submit($submit)
    {
        $this->submit = $submit;

        return $this;
    }

    public function render()
    {
        $attributes = new ComponentAttributeBag($this->attributes);
        $submit = new ComponentAttributeBag(is_array($this->submit) ? $this->submit : ['label' => $this->submit]);

        return Blade::render(<<<'BLADE'
            <tk:form :$attributes>
                @foreach ($elements as $element)
                    {{ $element }}
                @endforeach

                <tk:submit class="w-full" :attributes="$submit" />
            </tk:form>
        BLADE, [
            'attributes' => $attributes,
            'elements' => $this->elements,
            'submit' => $submit,
        ]);
    }

    public function toHtml()
    {
        return $this->render();
    }

    public function __toString()
    {
        return $this->render();
    }
}

<?php

namespace TALLKit\View;

use AllowDynamicProperties;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\View\Component;
use TALLKit\Concerns\Componentable;

#[AllowDynamicProperties]
class BladeComponent extends Component
{
    use Componentable;

    public static array $aliases = [];

    protected array $props = [];

    private array $setVariables = [];

    private array $smartAttributes = [];

    protected function props()
    {
        return $this->props;
    }

    public function resolveView()
    {
        $view = $this->render();

        if ($view instanceof View) {
            return $view;
        }

        $resolver = function ($view) {
            if ($view instanceof ViewContract) {
                return new HtmlString($view->render());
            }

            return $this->extractBladeViewFromString($view);
        };

        if ($view instanceof Closure) {
            return fn (array $data = []) => $resolver($view($data));
        }

        return function (array $data = []) use ($resolver, $view) {
            return view($resolver($view))->with($this->run($data));
        };
    }

    public function render()
    {
        return fn (array $data) => $this->blade()->with($this->run($data));
    }

    protected function setupProps($data)
    {
        foreach ($this->props() as $key => $prop) {
            $this->manageProps($key, $prop, $data);
        }
    }

    private function manageProps(string $key, mixed $prop, array $data)
    {
        $field = str($key)->camel()->toString();
        $this->{$field} = data_get($data, $key, $this->getData($field, $prop));
        $this->setVariables($field);
    }

    protected function setVariables($variables)
    {
        collect(Arr::wrap($variables))->filter()->each(
            fn ($value) => $this->setVariables[] = $value,
        );
    }

    protected function smartAttributes($attributes): void
    {
        collect(Arr::wrap($attributes))->filter()->each(
            fn ($value) => $this->smartAttributes[] = $value,
        );
    }

    protected function getData(string $attribute, mixed $default = null)
    {
        if ($this->attributes->has($kebab = str($attribute)->kebab()->toString())) {
            $this->smartAttributes($kebab);

            return $this->attributes->get($kebab);
        }

        if ($this->attributes->has($camel = str($attribute)->camel()->toString())) {
            $this->smartAttributes($camel);

            return $this->attributes->get($camel);
        }

        return $default;
    }

    protected function run(array $data)
    {
        $this->setupProps($data);

        $this->mounted($data);

        foreach ($this->setVariables as $attribute) {
            $data[$attribute] = $this->{$attribute};
        }

        $this->processed($data);

        $data['attributes'] = $this->attributes->except($this->smartAttributes);

        return $data;
    }

    public function classes(...$classes)
    {
        return new ClassBuilder($classes);
    }

    protected function mounted(array $data)
    {
        //
    }

    protected function processed(array $data)
    {
        //
    }

    public function attributesFromSlot($prop)
    {
        return $prop instanceof \Illuminate\View\ComponentSlot
            ? $prop->attributes
            : new \Illuminate\View\ComponentAttributeBag;
    }

    public function attributesAfter($prefix, $default = [], $mergeWithSlot = true)
    {
        $newAttributes = new \Illuminate\View\ComponentAttributeBag($default);

        foreach ($this->attributes->whereStartsWith($prefix)->getAttributes() as $key => $value) {
            $newAttributes[substr($key, strlen($prefix))] = $value;
        }

        if (! $mergeWithSlot) {
            return $newAttributes;
        }

        $prop = str(is_string($mergeWithSlot) ? $mergeWithSlot : $prefix)
            ->replaceLast(':', '')
            ->toString();

        if (! property_exists($this, $prop)) {
            return $newAttributes;
        }

        return $newAttributes->merge($this->attributesFromSlot($this->{$prop})->getAttributes());
    }
}

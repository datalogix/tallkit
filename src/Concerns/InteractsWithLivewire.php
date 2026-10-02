<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportQueryString\BaseUrl;
use Livewire\Livewire;
use Livewire\Mechanisms\ExtendBlade\ExtendBlade;

trait InteractsWithLivewire
{
    protected static ?bool $livewireInstalled = null;

    protected ?\WeakMap $livewireQueries = null;

    public function livewireInstalled(): bool
    {
        return static::$livewireInstalled ??= class_exists(Livewire::class);
    }

    public function livewireRendering(): bool
    {
        return $this->livewireInstalled() && ExtendBlade::isRenderingLivewireComponent();
    }

    // Livewire's current() is false when there is no component.
    public function livewireComponent(): ?Component
    {
        return $this->livewireInstalled() ? (app('livewire')->current() ?: null) : null;
    }

    protected function livewireComponentId(): ?string
    {
        return $this->livewireComponent()?->getId();
    }

    protected function livewireRequest(): bool
    {
        return $this->livewireInstalled() && app('livewire')->isLivewireRequest();
    }

    protected function livewireOriginalPath(): string
    {
        return $this->livewireRequest() ? (string) app('livewire')->originalPath() : request()->path();
    }

    protected function livewireOriginalQuery(): array
    {
        if (! $this->livewireRequest()) {
            return request()->query();
        }

        $component = $this->livewireComponent();

        $this->livewireQueries ??= new \WeakMap;
        $key = $component ?? request();

        [$query, $urlProperties] = $this->livewireQueries[$key] ??= [
            (function () {
                parse_str((string) parse_url((string) request()->headers->get('referer'), PHP_URL_QUERY), $query);

                return $query;
            })(),
            $component ? $this->livewireUrlProperties($component) : [],
        ];

        foreach ($urlProperties as $name => $property) {
            if (! $property->isInitialized($component)) {
                continue;
            }

            $value = $property->getValue($component);
            $value = $value instanceof \BackedEnum ? $value->value : $value;

            if ($value === null || $value === '' || $value === []) {
                unset($query[$name]);
            } else {
                $query[$name] = $value;
            }
        }

        return $query;
    }

    protected function livewireUrlProperties(Component $component): array
    {
        $properties = [];

        foreach ((new \ReflectionObject($component))->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $url = $property->getAttributes(BaseUrl::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;

            if ($url) {
                $properties[$url->newInstance()->as ?? $property->getName()] = $property;
            }
        }

        return $properties;
    }

    public function livewireComponentRendered(): bool
    {
        return $this->livewireInstalled() && Livewire::componentHasBeenRendered();
    }

    public function livewireLiveModel(ComponentAttributeBag $attributes): ?string
    {
        if (! $this->livewireInstalled()) {
            return null;
        }

        $directive = $attributes->wire('model');

        return $directive?->directive && $directive->hasModifier('live') ? $directive->value() : null;
    }

    public function livewirePropertyIs(string $name, string $class): bool
    {
        $component = $this->livewireComponent();
        $parent = str_contains($name, '.') ? data_get($component, Str::beforeLast($name, '.')) : $component;
        $property = Str::afterLast($name, '.');

        if (! is_object($parent) || ! property_exists($parent, $property)) {
            return false;
        }

        $type = (new \ReflectionProperty($parent, $property))->getType();
        $types = $type instanceof \ReflectionUnionType ? $type->getTypes() : array_filter([$type]);

        foreach ($types as $type) {
            if ($type instanceof \ReflectionNamedType && is_a($type->getName(), $class, true)) {
                return true;
            }
        }

        return false;
    }

    protected function livewireHasProperty(string $name): bool
    {
        $component = $this->livewireComponent();
        $property = Str::before($name, '.');

        if (! $component || ! property_exists($component, $property)) {
            return false;
        }

        $reflection = new \ReflectionProperty($component, $property);

        return $reflection->isPublic()
            && $reflection->getAttributes(Locked::class) === [];
    }
}

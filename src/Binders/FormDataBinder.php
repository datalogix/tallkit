<?php

namespace TALLKit\Binders;

use Illuminate\Support\Arr;

class FormDataBinder
{
    protected $bindings = [];

    protected $wire = false;

    protected $model = false;

    public function bind($target)
    {
        $this->bindings[] = $target;

        return $this;
    }

    public function getBind()
    {
        return Arr::last($this->bindings);
    }

    public function endBind()
    {
        array_pop($this->bindings);

        return $this;
    }

    public function isWired()
    {
        return $this->wire !== false;
    }

    public function wire(?string $modifier = null)
    {
        $this->wire = $modifier;

        return $this;
    }

    public function endWire()
    {
        $this->wire = false;

        return $this;
    }

    public function getWireModifier()
    {
        return is_string($this->wire) ? $this->wire : null;
    }

    public function isModel()
    {
        return $this->model !== false && ! $this->isWired();
    }

    public function model(?string $modifier = null)
    {
        $this->model = $modifier;

        return $this;
    }

    public function endModel()
    {
        $this->model = false;

        return $this;
    }

    public function getModelModifier()
    {
        return is_string($this->model) ? $this->model : null;
    }
}

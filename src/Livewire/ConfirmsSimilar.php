<?php

namespace TALLKit\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;

trait ConfirmsSimilar
{
    #[Locked]
    public array $similar = [];

    abstract protected function findSimilar(): iterable;

    abstract protected function storeSimilar();

    public function saveSimilar()
    {
        $attribute = $this->attributeSimilar();
        $field = $attribute?->getName();

        if ($field && is_string($this->{$field})) {
            $this->{$field} = Str::squish($this->{$field});
        }

        if ($this->getRules()) {
            $this->validate();
        }

        $record = $attribute?->getRecord();
        $unchanged = $record?->exists && $record->getOriginal($field) === $this->{$field};

        $this->similar = $unchanged ? [] : collect($this->findSimilar())->values()->all();

        return $this->similar ? null : $this->persistSimilar();
    }

    public function confirmSimilar()
    {
        if (! $this->similar) {
            return $this->saveSimilar();
        }

        if ($this->getRules()) {
            $this->validate();
        }

        $this->reset('similar');

        return $this->persistSimilar();
    }

    public function cancelSimilar(): void
    {
        $this->reset('similar');
    }

    protected function querySimilar(Builder|Relation $query, ?string $column = null, int $threshold = 75, int $minLength = 3): Collection
    {
        $attribute = $this->attributeSimilar();
        $field = $attribute?->getName();
        $record = $attribute?->getRecord();
        $column ??= $field;
        $slug = Str::slug((string) ($field ? $this->{$field} : ''));

        if ($slug === '' || ! $column) {
            return collect();
        }

        $model = $query->getModel();

        if ($record?->exists && $record instanceof $model) {
            $query->whereKeyNot($record->getKey());
        }

        $key = $model->getKeyName();

        return $query->get([$key, $column])
            ->filter(function ($model) use ($column, $slug, $threshold, $minLength) {
                $other = Str::slug((string) $model->{$column});

                if (min(strlen($slug), strlen($other)) >= $minLength && (str_contains($other, $slug) || str_contains($slug, $other))) {
                    return true;
                }

                similar_text($slug, $other, $percent);

                return $percent >= $threshold;
            })
            ->map(fn ($model) => $model->only([$key, $column]))
            ->values();
    }

    protected function attributeSimilar(): ?Similar
    {
        return $this->getAttributes()->whereInstanceOf(Similar::class)->first();
    }

    protected function persistSimilar()
    {
        try {
            return $this->storeSimilar();
        } catch (UniqueConstraintViolationException $e) {
            if (! $field = $this->attributeSimilar()?->getName()) {
                throw $e;
            }

            $this->addError($field, __('validation.unique', ['attribute' => str_replace('_', ' ', Str::snake($field))]));
        }
    }
}

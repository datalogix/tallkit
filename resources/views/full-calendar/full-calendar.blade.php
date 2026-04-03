@props([
    'options' => null,
])
<div
    wire:ignore
    x-data="fullCalendar(@js(array_replace_recursive(['locale' => Str::lower(Str::replace('_', '-', app()->getLocale()))], Arr::wrap($options))))"
    {{ $attributes }}
></div>

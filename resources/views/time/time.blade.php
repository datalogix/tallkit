@props([
    'value' => null,
    'format' => 'LLL',
    'fallback' => null,
])
@php

$time = filled($value) ? \Illuminate\Support\Carbon::parse($value)->locale(app()->getLocale()) : null;

@endphp
@if ($time)
    <time
        {{ $attributes->merge(['datetime' => $time->toIso8601String(), 'title' => $time->isoFormat($format)]) }}
    >{{ $slot->isEmpty() ? $time->diffForHumans() : $slot }}</time>
@elseif ($fallback)
    <span {{ $attributes }}>{{ __($fallback) }}</span>
@endif

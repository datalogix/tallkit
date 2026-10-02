<?php

namespace TALLKit\Concerns;

use Carbon\Carbon;

trait InteractsWithLocale
{
    public function resolveLocale(?string $locale = null): string
    {
        return str_replace('_', '-', $locale ?: app()->getLocale());
    }

    public function localeFirstDay(?string $locale = null): int
    {
        $firstDay = Carbon::now()->locale($locale ?: app()->getLocale())->getTranslationMessage('first_day_of_week');

        return is_numeric($firstDay) ? (int) $firstDay % 7 : 1;
    }
}

<?php

namespace TALLKit\Concerns;

use Carbon\Carbon;

trait InteractsWithLocale
{
    public function locale(): string
    {
        return app()->getLocale();
    }

    public function resolveLocale(?string $locale = null): string
    {
        return str_replace('_', '-', $locale ?: $this->locale());
    }

    public function localeFirstDay(?string $locale = null): int
    {
        $firstDay = Carbon::now()->locale($locale ?: $this->locale())->getTranslationMessage('first_day_of_week');

        return is_numeric($firstDay) ? (int) $firstDay % 7 : 1;
    }
}

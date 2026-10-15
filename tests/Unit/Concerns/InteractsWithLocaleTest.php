<?php

namespace TALLKit\Tests\Unit\Concerns;

use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithLocaleTest extends TestCase
{
    public function test_locale_follows_the_app_locale(): void
    {
        app()->setLocale('pt_BR');

        $this->assertSame('pt_BR', TALLKit::locale());
    }

    public function test_resolve_locale_uses_dashes(): void
    {
        app()->setLocale('pt_BR');

        $this->assertSame('pt-BR', TALLKit::resolveLocale());
        $this->assertSame('en-US', TALLKit::resolveLocale('en_US'));
        $this->assertSame('pt-BR', TALLKit::resolveLocale(''));
    }

    public function test_locale_first_day(): void
    {
        app()->setLocale('en_US');

        $this->assertSame(0, TALLKit::localeFirstDay());
        $this->assertSame(1, TALLKit::localeFirstDay('pt_PT'));
        $this->assertSame(1, TALLKit::localeFirstDay('de'));
        $this->assertSame(6, TALLKit::localeFirstDay('ar_EG'));
    }

    public function test_locale_first_day_defaults_to_monday_when_unknown(): void
    {
        $this->assertSame(1, TALLKit::localeFirstDay('zz_unknown'));
    }
}

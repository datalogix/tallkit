<?php

namespace TALLKit\Tests\Feature\View;

use Illuminate\Support\Facades\Blade;
use TALLKit\Tests\TestCase;

class AuthProfileTest extends TestCase
{
    public function test_it_asks_for_the_name(): void
    {
        $html = Blade::render('<tk:page.auth.profile />');

        $this->assertStringContainsString('name="name"', $html);
        $this->assertStringNotContainsString('name="locale"', $html);
        $this->assertStringNotContainsString('name="timezone"', $html);
    }

    public function test_it_offers_the_languages_and_time_zones_given(): void
    {
        $html = Blade::render('<tk:page.auth.profile :locales="[\'en\' => \'English\', \'pt_BR\' => \'Português\']" :timezones="[\'America\' => [\'America/Sao_Paulo\' => \'Sao Paulo\']]" />');

        $this->assertStringContainsString('name="locale"', $html);
        $this->assertStringContainsString('Português', $html);
        $this->assertStringContainsString('name="timezone"', $html);
        $this->assertStringContainsString('America/Sao_Paulo', $html);
        $this->assertStringContainsString('<optgroup', $html);
    }

    public function test_the_fields_of_the_application_go_before_the_button(): void
    {
        $html = Blade::render('<tk:page.auth.profile><input name="phone" /></tk:page.auth.profile>');

        $this->assertLessThan(strpos($html, 'Save'), strpos($html, 'name="phone"'));
    }

    public function test_it_offers_a_photo_when_asked(): void
    {
        $this->assertStringNotContainsString('name="avatar"', Blade::render('<tk:page.auth.profile />'));

        $html = Blade::render('<tk:page.auth.profile avatar avatar-max-size="512" />');

        $this->assertStringContainsString('name="avatar"', $html);
        $this->assertStringContainsString('image/png,image/jpeg,image/webp', $html);
    }
}

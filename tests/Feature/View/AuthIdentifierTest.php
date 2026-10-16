<?php

namespace TALLKit\Tests\Feature\View;

use Illuminate\Support\Facades\Blade;
use TALLKit\Tests\TestCase;

class AuthIdentifierTest extends TestCase
{
    public function test_the_field_follows_the_identifier(): void
    {
        $html = Blade::render('<tk:page.auth.identifier identifier="cpf" />');

        $this->assertStringContainsString('name="cpf"', $html);
        $this->assertStringContainsString('CPF', $html);
        $this->assertStringContainsString('placeholder="000.000.000-00"', $html);
        $this->assertStringContainsString('x-mask', $html);
    }

    public function test_the_field_takes_what_is_given_in_place_of_the_identifier_defaults(): void
    {
        $html = Blade::render('<tk:page.auth.identifier identifier="registration" name="login" label="Registration" placeholder="0000-0" mask="9999-9" />');

        $this->assertStringContainsString('name="login"', $html);
        $this->assertStringContainsString('Registration', $html);
        $this->assertStringContainsString('placeholder="0000-0"', $html);
        $this->assertStringContainsString('x-mask="9999-9"', $html);
        $this->assertStringNotContainsString('email@example.com', $html);
    }

    public function test_the_forgot_password_subtitle_can_be_given(): void
    {
        $default = Blade::render('<tk:page.auth.forgot-password identifier="cpf" />');
        $given = Blade::render('<tk:page.auth.forgot-password identifier="registration" subtitle="Enter your registration." />');

        $this->assertStringContainsString('Enter your CPF', $default);
        $this->assertStringContainsString('Enter your registration.', $given);
        $this->assertStringNotContainsString('Enter your email address', $given);
    }
}

<?php

namespace TALLKit\Tests\Feature\View;

use Illuminate\Support\Facades\Blade;
use TALLKit\Tests\TestCase;

class AuthEmailLoginTest extends TestCase
{
    public function test_it_asks_for_the_login_to_send_a_link_by_default(): void
    {
        $html = Blade::render('<tk:page.auth.email-login login-url="/login" />');

        $this->assertStringContainsString('Sign in with a link', $html);
        $this->assertStringContainsString('Send sign-in link', $html);
        $this->assertStringContainsString('name="email"', $html);
        $this->assertStringNotContainsString('name="code"', $html);
    }

    public function test_the_code_mode_sends_a_code(): void
    {
        $html = Blade::render('<tk:page.auth.email-login mode="code" login-url="/login" />');

        $this->assertStringContainsString('Sign in with a code', $html);
        $this->assertStringContainsString('Send sign-in code', $html);
    }

    public function test_both_modes_tell_of_the_link_and_the_code(): void
    {
        $html = Blade::render('<tk:page.auth.email-login mode="both" login-url="/login" />');

        $this->assertStringContainsString('a link and a code', $html);
        $this->assertStringContainsString('Send sign-in link', $html);
    }

    public function test_the_code_step_asks_for_the_code(): void
    {
        $html = Blade::render('<tk:page.auth.email-login mode="code" code-step login-url="/login" />');

        $this->assertStringContainsString('Enter the 6-digit code we e-mailed you.', $html);
        $this->assertStringContainsString('name="code"', $html);
        $this->assertStringNotContainsString('Send sign-in code', $html);
    }

    public function test_the_code_step_of_both_modes_also_points_to_the_link(): void
    {
        $html = Blade::render('<tk:page.auth.email-login mode="both" code-step login-url="/login" />');

        $this->assertStringContainsString('or open the link in the e-mail', $html);
    }

    public function test_the_confirmation_comes_first(): void
    {
        $html = Blade::render('<tk:page.auth.email-login mode="both" code-step confirm-email="jane@example.com" />');

        $this->assertStringContainsString('jane@example.com', $html);
        $this->assertStringNotContainsString('name="code"', $html);
    }
}

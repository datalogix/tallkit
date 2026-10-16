<?php

namespace TALLKit\Tests\Feature\View;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ViewErrorBag;
use TALLKit\Tests\TestCase;

class AuthSignUpTest extends TestCase
{
    public function test_the_fields_of_its_slot_go_after_the_password_and_before_the_button(): void
    {
        app('view')->share('errors', new ViewErrorBag);

        $html = Blade::render('<tk:page.auth.sign-up :terms="false"><input name="phone" /></tk:page.auth.sign-up>');

        $this->assertGreaterThan(strpos($html, 'name="password_confirmation"'), strpos($html, 'name="phone"'));
        $this->assertLessThan(strpos($html, 'Create account'), strpos($html, 'name="phone"'));
    }
}

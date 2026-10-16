<?php

namespace TALLKit\Tests\Feature\View;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ViewErrorBag;
use TALLKit\Tests\TestCase;

class AuthAcceptTermsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        app('view')->share('errors', new ViewErrorBag);
    }

    public function test_each_document_is_shown_and_ticked_on_its_own(): void
    {
        $html = Blade::render('<tk:page.auth.accept-terms :documents="$documents" logout-url="/logout" />', ['documents' => [
            ['key' => 'terms-of-use', 'label' => 'Terms of use', 'url' => null, 'html' => '<p>The rules of the house.</p>'],
            ['key' => 'privacy-policy', 'label' => 'Privacy policy', 'url' => 'https://example.com/privacy', 'html' => null],
            ['key' => 'contract', 'label' => 'Contract', 'url' => null, 'html' => null, 'pdf' => '/terms/accept/contract.pdf'],
        ]]);

        $this->assertStringContainsString('<p>The rules of the house.</p>', $html);
        $this->assertStringContainsString('name="accepted.terms-of-use"', $html);
        $this->assertStringContainsString('name="accepted.privacy-policy"', $html);
        $this->assertStringContainsString('href="https://example.com/privacy"', $html);
        $this->assertStringContainsString('<iframe', $html);
        $this->assertStringContainsString('src="/terms/accept/contract.pdf"', $html);
        $this->assertStringContainsString('Accept and continue', $html);
        $this->assertStringContainsString('href="/logout"', $html);
    }

    public function test_without_a_logout_url_there_is_no_link_to_log_out(): void
    {
        $this->assertStringNotContainsString('Log out', Blade::render('<tk:page.auth.accept-terms />'));
    }
}

<?php

namespace TALLKit\Tests\Unit\Concerns;

use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithOAuthTest extends TestCase
{
    public function test_oauth_icons(): void
    {
        $icons = TALLKit::oauthIcons();

        $this->assertCount(15, $icons);
        $this->assertSame('logos:google-icon', $icons['google']);
        $this->assertSame('simple-icons:x', $icons['twitter-oauth-2']);
    }

    public function test_oauth_icon(): void
    {
        $this->assertSame('logos:github-icon', TALLKit::oauthIcon('github'));
        $this->assertSame('simple-icons:apple', TALLKit::oauthIcon('apple'));
        $this->assertSame('ph:key', TALLKit::oauthIcon('keycloak'));
    }

    public function test_oauth_name(): void
    {
        $this->assertSame('GitHub', TALLKit::oauthName('github'));
        $this->assertSame('LinkedIn', TALLKit::oauthName('linkedin-openid'));
        $this->assertSame('X', TALLKit::oauthName('twitter'));
        $this->assertSame('Slack', TALLKit::oauthName('slack-openid'));
        $this->assertSame('Google', TALLKit::oauthName('google'));
        $this->assertSame('Azure Ad', TALLKit::oauthName('azure_ad'));
    }
}

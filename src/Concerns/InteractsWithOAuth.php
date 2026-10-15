<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Str;

trait InteractsWithOAuth
{
    public function oauthIcons(): array
    {
        return [
            'google' => 'logos:google-icon',
            'github' => 'logos:github-icon',
            'gitlab' => 'logos:gitlab',
            'facebook' => 'logos:facebook',
            'apple' => 'simple-icons:apple',
            'microsoft' => 'logos:microsoft-icon',
            'discord' => 'logos:discord-icon',
            'linkedin' => 'logos:linkedin-icon',
            'linkedin-openid' => 'logos:linkedin-icon',
            'bitbucket' => 'logos:bitbucket',
            'slack' => 'logos:slack-icon',
            'slack-openid' => 'logos:slack-icon',
            'twitter' => 'simple-icons:x',
            'twitter-oauth-2' => 'simple-icons:x',
            'x' => 'simple-icons:x',
        ];
    }

    public function oauthIcon(string $provider): string
    {
        return $this->oauthIcons()[$provider] ?? 'ph:key';
    }

    public function oauthName(string $provider): string
    {
        return [
            'github' => 'GitHub',
            'gitlab' => 'GitLab',
            'linkedin' => 'LinkedIn',
            'linkedin-openid' => 'LinkedIn',
            'twitter' => 'X',
            'twitter-oauth-2' => 'X',
            'x' => 'X',
            'slack-openid' => 'Slack',
        ][$provider] ?? Str::headline($provider);
    }
}

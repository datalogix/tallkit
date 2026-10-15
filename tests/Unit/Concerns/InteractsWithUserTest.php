<?php

namespace TALLKit\Tests\Unit\Concerns;

use Illuminate\Auth\GenericUser;
use Illuminate\View\ComponentAttributeBag;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithUserTest extends TestCase
{
    public function test_user_context_without_user(): void
    {
        $this->assertSame([null, null, null, null], TALLKit::userContext(new ComponentAttributeBag));
    }

    public function test_user_context_from_authenticated_user(): void
    {
        $user = new GenericUser(['id' => 1, 'name' => 'Jane', 'email' => 'jane@example.com', 'username' => 'jane']);
        $this->actingAs($user);

        $attributes = new ComponentAttributeBag(['class' => 'x']);

        $this->assertSame([$user, 'Jane', 'jane@example.com', 'jane'], TALLKit::userContext($attributes));
        $this->assertSame(['class' => 'x'], $attributes->getAttributes());
    }

    public function test_user_context_uses_guard(): void
    {
        config(['auth.guards.admin' => ['driver' => 'session', 'provider' => 'users']]);

        $admin = new GenericUser(['id' => 2, 'name' => 'Admin']);
        auth()->guard('admin')->setUser($admin);

        $attributes = new ComponentAttributeBag(['guard' => 'admin']);

        $this->assertSame([$admin, 'Admin', null, null], TALLKit::userContext($attributes));
        $this->assertSame([], $attributes->getAttributes());
        $this->assertSame([null, null, null, null], TALLKit::userContext(new ComponentAttributeBag));
    }

    public function test_user_context_with_given_user_and_overrides(): void
    {
        $this->actingAs(new GenericUser(['id' => 1, 'name' => 'Auth']));

        $user = ['name' => 'Given', 'email' => 'given@example.com'];
        $attributes = new ComponentAttributeBag(['user' => $user, 'email' => 'other@example.com']);

        $this->assertSame([$user, 'Given', 'other@example.com', null], TALLKit::userContext($attributes));
        $this->assertSame([], $attributes->getAttributes());
    }

    public function test_user_context_with_explicit_fields_skips_auth_user(): void
    {
        $this->actingAs(new GenericUser(['id' => 1, 'name' => 'Auth', 'email' => 'auth@example.com']));

        $this->assertSame([null, null, null, 'bob'], TALLKit::userContext(new ComponentAttributeBag(['username' => 'bob'])));
        $this->assertSame([null, 'Bob', null, null], TALLKit::userContext(new ComponentAttributeBag(['name' => 'Bob'])));
    }
}

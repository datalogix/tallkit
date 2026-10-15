<?php

namespace TALLKit\Tests\Unit\Concerns;

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentSlot;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithErrorBagsTest extends TestCase
{
    protected function tearDown(): void
    {
        app('view')->flushState();

        parent::tearDown();
    }

    protected function shareErrors(array $default = [], array $login = []): void
    {
        $errors = (new ViewErrorBag)
            ->put('default', new MessageBag($default))
            ->put('login', new MessageBag($login));

        app('view')->share('errors', $errors);
    }

    public function test_error_bag_is_empty_without_errors(): void
    {
        $bag = TALLKit::errorBag();

        $this->assertInstanceOf(MessageBag::class, $bag);
        $this->assertTrue($bag->isEmpty());
    }

    public function test_error_bag_reads_shared_errors_and_named_bags(): void
    {
        $this->shareErrors(['email' => ['Invalid email']], ['password' => ['Wrong password']]);

        $this->assertSame('Invalid email', TALLKit::errorBag()->first('email'));
        $this->assertSame('Wrong password', TALLKit::errorBag('login')->first('password'));
    }

    public function test_error_bag_falls_back_to_session_errors(): void
    {
        $this->app['session']->put('errors', (new ViewErrorBag)->put('default', new MessageBag(['name' => ['Required']])));

        $this->assertSame('Required', TALLKit::errorBag()->first('name'));
    }

    public function test_error_bag_uses_the_error_bag_of_the_parent_component(): void
    {
        $this->shareErrors(['email' => ['Default bag']], ['email' => ['Login bag']]);

        app('view')->startComponent('form', ['errorBag' => 'login']);

        try {
            $this->assertSame('Login bag', TALLKit::errorBag()->first('email'));
            $this->assertSame('Default bag', TALLKit::errorBag('default')->first('email'));
        } finally {
            ob_end_clean();
        }
    }

    public function test_has_error_converts_brackets_and_matches_nested_keys(): void
    {
        $this->shareErrors([
            'user.email' => ['Invalid'],
            'tags.0' => ['Bad tag'],
            'roles.1.name' => ['Bad role'],
        ]);

        $this->assertTrue(TALLKit::hasError('user[email]'));
        $this->assertTrue(TALLKit::hasError('user.email'));
        $this->assertTrue(TALLKit::hasError('tags[]'));
        $this->assertTrue(TALLKit::hasError('tags'));
        $this->assertTrue(TALLKit::hasError('roles[][name]'));
        $this->assertFalse(TALLKit::hasError('name'));
        $this->assertFalse(TALLKit::hasError('user[email]', 'login'));
    }

    public function test_error_message(): void
    {
        $this->shareErrors(['user.email' => ['Invalid'], 'tags.0' => ['Bad tag']], ['email' => ['Login']]);
        $slot = new ComponentSlot('Custom');

        $this->assertSame('Invalid', TALLKit::errorMessage('user[email]'));
        $this->assertSame('Bad tag', TALLKit::errorMessage('tags[]'));
        $this->assertSame('Login', TALLKit::errorMessage('email', bag: 'login'));
        $this->assertSame('', TALLKit::errorMessage('missing'));
        $this->assertSame('Invalid', TALLKit::errorMessage('user.email', $slot));
        $this->assertSame($slot, TALLKit::errorMessage(slot: $slot));
        $this->assertNull(TALLKit::errorMessage());
    }
}

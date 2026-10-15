<?php

namespace TALLKit\Tests\Unit\Concerns;

use Illuminate\Support\Facades\Session;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithAlertTest extends TestCase
{
    protected function alert(array $values = []): array
    {
        return [
            'message' => null,
            'type' => null,
            'variant' => null,
            'icon' => null,
            'border' => null,
            'title' => null,
            'closable' => null,
            'duration' => null,
            'size' => null,
            'progress' => null,
            'pauseOnHover' => null,
            ...$values,
        ];
    }

    public function test_alert_is_flashed_to_status(): void
    {
        $this->startSession();

        $this->assertNull(TALLKit::alert('Saved', 'success', 'solid', 'check', 'left', 'Done', true, 3000, 'sm', null, 'bar', true));

        $this->assertSame($this->alert([
            'message' => 'Saved',
            'type' => 'success',
            'variant' => 'solid',
            'icon' => 'check',
            'border' => 'left',
            'title' => 'Done',
            'closable' => true,
            'duration' => 3000,
            'size' => 'sm',
            'progress' => 'bar',
            'pauseOnHover' => true,
        ]), Session::get('status'));
        $this->assertContains('status', Session::get('_flash.new'));
    }

    public function test_alerts_in_the_same_request_accumulate(): void
    {
        $this->startSession();

        TALLKit::alert('One');
        TALLKit::alert('Two');
        TALLKit::alert(title: 'Three');

        $this->assertSame([
            $this->alert(['message' => 'One']),
            $this->alert(['message' => 'Two']),
            $this->alert(['title' => 'Three']),
        ], Session::get('status'));
    }

    public function test_alert_joins_a_flashed_string_status(): void
    {
        $this->startSession();

        Session::flash('status', 'Profile updated');
        TALLKit::alert('Saved');

        $this->assertSame(['Profile updated', $this->alert(['message' => 'Saved'])], Session::get('status'));
    }

    public function test_alert_replaces_a_status_from_a_previous_request(): void
    {
        $this->startSession();

        Session::put('status', 'old');
        TALLKit::alert('Saved');

        $this->assertSame($this->alert(['message' => 'Saved']), Session::get('status'));
    }

    public function test_alert_with_name_and_array_message(): void
    {
        $this->startSession();

        TALLKit::alert(['First', 'Second'], name: 'errors-box');

        $this->assertSame($this->alert(['message' => ['First', 'Second']]), Session::get('errors-box'));
        $this->assertNull(Session::get('status'));
    }

    public function test_blank_alert_is_ignored(): void
    {
        $this->startSession();

        $this->assertNull(TALLKit::alert('', title: ' '));
        $this->assertNull(TALLKit::alert(null));

        $this->assertNull(Session::get('status'));
    }

    public function test_alert_without_session_is_ignored(): void
    {
        $this->assertNull(TALLKit::alert('Saved'));
        $this->assertFalse($this->app['session']->isStarted());
    }

    public function test_type_proxy(): void
    {
        $this->startSession();

        $this->assertNull(TALLKit::alert()->danger('Failed', 'Oops', 'outline'));

        $this->assertSame(
            $this->alert(['message' => 'Failed', 'type' => 'danger', 'variant' => 'outline', 'title' => 'Oops']),
            Session::get('status'),
        );
    }

    public function test_type_proxy_close_is_not_special_for_alerts(): void
    {
        $this->startSession();

        TALLKit::alert()->close('Closed');

        $this->assertSame($this->alert(['message' => 'Closed', 'type' => 'close']), Session::get('status'));
    }
}

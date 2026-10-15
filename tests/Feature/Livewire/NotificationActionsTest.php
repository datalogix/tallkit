<?php

namespace TALLKit\Tests\Feature\Livewire;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\Livewire;
use TALLKit\Tests\TestCase;

class NotificationActionsTest extends TestCase
{
    protected NotifiableUser $user;

    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        $app['config']->set('database.default', 'testing');
        $app['config']->set('auth.providers.users.model', NotifiableUser::class);
        $app['config']->set('auth.guards.admin', ['driver' => 'session', 'provider' => 'users']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        $this->user = NotifiableUser::create(['name' => 'Ana']);
        $other = NotifiableUser::create(['name' => 'Bia']);

        foreach (['n1' => $this->user, 'n2' => $this->user, 'n3' => $this->user, 'r1' => $this->user, 'o1' => $other] as $id => $owner) {
            DB::table('notifications')->insert([
                'id' => $id,
                'type' => 'test',
                'notifiable_type' => NotifiableUser::class,
                'notifiable_id' => $owner->id,
                'data' => '{}',
                'read_at' => str_starts_with($id, 'r') ? now() : null,
            ]);
        }
    }

    protected function unread(): array
    {
        return DB::table('notifications')->whereNull('read_at')->orderBy('id')->pluck('id')->all();
    }

    protected function ids(): array
    {
        return DB::table('notifications')->orderBy('id')->pluck('id')->all();
    }

    protected function livewire(?string $guard = null)
    {
        $component = $guard === null ? new class extends Component
        {
            public function ping()
            {
                return 'pong';
            }

            public function render()
            {
                return '<div></div>';
            }
        } : new class extends Component
        {
            public $notificationGuard = 'unknown';

            public function render()
            {
                return '<div></div>';
            }
        };

        return Livewire::test($component);
    }

    public function test_mark_as_read(): void
    {
        $this->actingAs($this->user);

        $this->livewire()->call('notificationMarkAsRead', 'n1');

        $this->assertSame(['n2', 'n3', 'o1'], $this->unread());
    }

    public function test_mark_as_read_only_touches_the_user_notifications(): void
    {
        $this->actingAs($this->user);

        $this->livewire()->call('notificationMarkAsRead', 'o1');

        $this->assertSame(['n1', 'n2', 'n3', 'o1'], $this->unread());
    }

    public function test_invalid_arguments_return_early(): void
    {
        $this->actingAs($this->user);

        $component = $this->livewire();

        foreach ([
            ['notificationMarkAsRead', ['n1']],
            ['notificationMarkAsRead', null],
            ['notificationMarkAsRead', 'n1', 123],
            ['notificationDelete', 1.5],
            ['notificationMarkAllAsRead', 'n1'],
            ['notificationMarkAllAsRead', ['a' => 'n1']],
            ['notificationMarkAllAsRead', ['n1', ['n2']]],
            ['notificationMarkAllAsRead', array_fill(0, 1001, 'n1')],
            ['notificationMarkAllAsRead', null, ['web']],
        ] as $arguments) {
            $this->assertSame([null], $component->call(...$arguments)->effects['returns'], $arguments[0]);
        }

        $this->assertSame(['n1', 'n2', 'n3', 'o1'], $this->unread());
    }

    public function test_mark_all_as_read(): void
    {
        $this->actingAs($this->user);

        $this->livewire()->call('notificationMarkAllAsRead', ['n1', 2]);
        $this->assertSame(['n2', 'n3', 'o1'], $this->unread());

        $this->livewire()->call('notificationMarkAllAsRead');
        $this->assertSame(['o1'], $this->unread());
    }

    public function test_delete_removes_only_read_notifications(): void
    {
        $this->actingAs($this->user);

        $component = $this->livewire();
        $component->call('notificationDelete', 'n1');
        $component->call('notificationDelete', 'r1');

        $this->assertSame(['n1', 'n2', 'n3', 'o1'], $this->ids());
    }

    public function test_numeric_id_is_cast_to_string(): void
    {
        $this->actingAs($this->user);

        DB::table('notifications')->where('id', 'r1')->update(['id' => '7']);

        $this->livewire()->call('notificationDelete', 7);

        $this->assertSame(['n1', 'n2', 'n3', 'o1'], $this->ids());
    }

    public function test_guard_from_the_browser_must_exist(): void
    {
        $this->actingAs($this->user);
        $this->actingAs($this->user, 'admin');

        $this->livewire()->call('notificationMarkAsRead', 'n1', 'missing');
        $this->assertSame(['n1', 'n2', 'n3', 'o1'], $this->unread());

        $this->livewire()->call('notificationMarkAsRead', 'n1', 'admin');
        $this->assertSame(['n2', 'n3', 'o1'], $this->unread());
    }

    public function test_component_guard_overrides_the_browser_guard(): void
    {
        $this->actingAs($this->user);

        $this->livewire('unknown')->call('notificationMarkAllAsRead', null, 'web');

        $this->assertSame(['n1', 'n2', 'n3', 'o1'], $this->unread());
    }

    public function test_guest_and_users_without_notifications_are_ignored(): void
    {
        $this->livewire()->call('notificationMarkAllAsRead');
        $this->assertSame(['n1', 'n2', 'n3', 'o1'], $this->unread());

        $this->actingAs(new PlainUser(['id' => $this->user->id]));
        $this->livewire()->call('notificationMarkAllAsRead');
        $this->assertSame(['n1', 'n2', 'n3', 'o1'], $this->unread());
    }

    public function test_other_methods_are_not_intercepted(): void
    {
        $this->assertSame(['pong'], $this->livewire()->call('ping')->effects['returns']);
    }
}

class NotifiableUser extends User
{
    use Notifiable;

    protected $table = 'users';

    protected $guarded = [];

    public $timestamps = false;
}

class PlainUser extends User
{
    protected $guarded = [];
}

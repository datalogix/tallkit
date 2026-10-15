<?php

namespace TALLKit\Tests\Unit\Concerns;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

enum InteractsWithTableStatus: string
{
    case Active = 'active';

    public function label(): string
    {
        return 'Is active';
    }
}

enum InteractsWithTablePriority: int
{
    case High = 3;
}

enum InteractsWithTableKind
{
    case Admin;
}

class InteractsWithTableUser extends Model
{
    protected $table = 'table_users';

    protected $guarded = [];

    protected $hidden = ['password'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(InteractsWithTableUser::class);
    }
}

class InteractsWithTableFillable extends Model
{
    protected $fillable = ['name', 'email', 'password', 'api_key'];

    protected $hidden = ['email'];
}

class InteractsWithTableNoTimestamps extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];
}

class InteractsWithTableVisible extends Model
{
    protected $visible = ['id', 'name', 'remember_token'];
}

class InteractsWithTableTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('database.default', 'testing');
    }

    protected function createUsers(): void
    {
        Schema::create('table_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('password')->nullable();
            $table->string('remember_token')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
        });

        InteractsWithTableUser::create(['name' => 'Bob']);
        InteractsWithTableUser::create(['name' => 'Ann']);
        InteractsWithTableUser::create(['name' => 'Cid']);
    }

    protected function withRequest(string $uri): void
    {
        $this->app->instance('request', Request::create($uri));
    }

    public function test_column_attributes_without_name(): void
    {
        $this->assertSame([], TALLKit::tableColumnAttributes(null)->getAttributes());
        $this->assertSame([], TALLKit::tableColumnAttributes('', toggleable: true)->getAttributes());
    }

    public function test_column_attributes_for_a_plain_column(): void
    {
        $this->assertSame(['data-column-key' => 'a&amp;b'], TALLKit::tableColumnAttributes('a&b')->getAttributes());
    }

    public function test_column_attributes_hidden_and_toggleable(): void
    {
        $attributes = TALLKit::tableColumnAttributes('name', toggleable: true, hidden: true)->getAttributes();

        $this->assertTrue($attributes['hidden']);
        $this->assertTrue($attributes['data-column-hidden']);
        $this->assertSame('isColumnHidden(\'name\')', $attributes['x-bind:hidden']);
    }

    public function test_column_attributes_sticky_left(): void
    {
        $attributes = TALLKit::tableColumnAttributes('user.name', sticky: 'left')->getAttributes();

        $this->assertSame('left', $attributes['data-column-sticky']);
        $this->assertTrue($attributes['data-column-pinned']);
        $this->assertSame('inset-inline-start: var(--tk-column-left-user-name, 0px)', $attributes['style']);
        $this->assertArrayNotHasKey('x-bind:data-column-pinned', $attributes);
    }

    public function test_column_attributes_pinnable_and_css_variable_escaping(): void
    {
        $attributes = TALLKit::tableColumnAttributes('ação 😀_x-y', pinnable: true)->getAttributes();

        // Laravel 12 escapes unicode in Js::from, Laravel 13 does not: same JS string either way.
        $this->assertMatchesRegularExpression('/^isColumnPinned\(\'(.+)\'\)$/', $attributes['x-bind:data-column-pinned']);
        $this->assertSame('ação 😀_x-y', json_decode('"'.substr($attributes['x-bind:data-column-pinned'], 16, -2).'"'));
        $this->assertSame('inset-inline-start: var(--tk-column-left-a--o---_x-y, 0px)', $attributes['style']);
        $this->assertArrayNotHasKey('data-column-sticky', $attributes);
    }

    public function test_column_attributes_sticky_other_than_left_is_ignored(): void
    {
        $this->assertSame(['data-column-key' => 'name'], TALLKit::tableColumnAttributes('name', sticky: true)->getAttributes());
    }

    public function test_sort_state(): void
    {
        $this->withRequest('/users');
        $this->assertSame([null, 'asc'], TALLKit::tableSortState());

        $this->withRequest('/users?sortBy=name&sortDirection=desc');
        $this->assertSame(['name', 'desc'], TALLKit::tableSortState());

        $this->withRequest('/users?sortBy=&sortDirection=other');
        $this->assertSame([null, 'asc'], TALLKit::tableSortState());

        $this->withRequest('/users?sortBy[]=name');
        $this->assertSame([null, 'asc'], TALLKit::tableSortState());
    }

    public function test_sort_href_keeps_query_and_drops_page(): void
    {
        $this->withRequest('/users?search=bob&page=3&sortBy=id');

        $this->assertSame(
            'http://localhost/users?search=bob&sortBy=name&sortDirection=desc',
            TALLKit::tableSortHref('name', 'desc'),
        );
    }

    public function test_sort_query_without_sort_returns_query_untouched(): void
    {
        $this->withRequest('/users');
        $query = InteractsWithTableUser::query();

        $this->assertSame($query, TALLKit::tableSortQuery($query, []));
        $this->assertSame([], $query->getQuery()->orders ?? []);
    }

    public function test_sort_query_with_default_columns(): void
    {
        $this->createUsers();

        $this->withRequest('/users?sortBy=name&sortDirection=desc');
        $this->assertSame(['Cid', 'Bob', 'Ann'], TALLKit::tableSortQuery(InteractsWithTableUser::query(), null)->pluck('name')->all());

        $this->withRequest('/users?sortBy=password');
        $this->assertSame([], TALLKit::tableSortQuery(InteractsWithTableUser::query(), [])->getQuery()->orders ?? []);
    }

    public function test_sort_query_with_explicit_columns(): void
    {
        $this->createUsers();
        $orders = function (array $columns, string $by): array {
            $this->withRequest("/users?sortBy={$by}");

            return collect(TALLKit::tableSortQuery(InteractsWithTableUser::query(), $columns)->getQuery()->orders ?? [])
                ->pluck('column')
                ->all();
        };

        $this->assertSame(['name'], $orders(['id', 'name'], 'name'));
        $this->assertSame(['name'], $orders(['name' => 'Name'], 'name'));
        $this->assertSame(['name'], $orders(['label' => ['name' => 'name']], 'name'));
        $this->assertSame(['name'], $orders(['name' => ['label' => 'Name']], 'name'));
        $this->assertSame([], $orders(['name' => ['sortable' => false]], 'name'));
        $this->assertSame([], $orders(['team.name'], 'team.name'));
        $this->assertSame([], $orders(['id', null], 'name'));
        $this->assertSame([], $orders([1 => ['name' => 5]], '5'));
    }

    public function test_default_columns_of_arrays_and_collections(): void
    {
        $this->assertSame(
            ['id', 'name'],
            TALLKit::tableDefaultColumns(['id' => 1, 'name' => 'A', 'password' => 'x', 'api_key' => 'k', 'two_factor_secret' => 's', 0 => 'z']),
        );
        $this->assertSame([], TALLKit::tableDefaultColumns(['otp_code' => 1, 'recovery_codes' => 2]));
        $this->assertSame([], TALLKit::tableDefaultColumns(null));
    }

    public function test_default_columns_of_a_model_with_visible(): void
    {
        $this->assertSame(['id', 'name'], TALLKit::tableDefaultColumns(new InteractsWithTableVisible));
    }

    public function test_default_columns_of_a_model_with_fillable(): void
    {
        $this->assertSame(['id', 'name', 'created_at', 'updated_at'], TALLKit::tableDefaultColumns(new InteractsWithTableFillable));
        $this->assertSame(['id', 'name'], TALLKit::tableDefaultColumns(new InteractsWithTableNoTimestamps));
    }

    public function test_default_columns_of_a_model_from_attributes_or_schema(): void
    {
        $this->createUsers();

        $this->assertSame(
            ['id', 'name', 'team_id', 'created_at', 'updated_at'],
            TALLKit::tableDefaultColumns(new InteractsWithTableUser),
        );
        $this->assertSame(['name', 'id'], TALLKit::tableDefaultColumns(new InteractsWithTableUser(['name' => 'x', 'id' => 1, 'password' => 'p'])));
    }

    public function test_cell_value_reads_formatted_custom_key_and_key(): void
    {
        $this->assertSame('F', TALLKit::tableCellValue(['name' => 'N', 'name_formatted' => 'F'], 'name', []));
        $this->assertSame('Nested', TALLKit::tableCellValue(['user' => ['name' => 'Nested']], 'name', ['_key' => 'user.name']));
        $this->assertSame('N', TALLKit::tableCellValue(['name' => 'N'], 'name', ['_key' => 'missing']));
    }

    public function test_cell_value_returns_scalars_untouched(): void
    {
        $this->assertNull(TALLKit::tableCellValue([], 'x', []));
        $this->assertTrue(TALLKit::tableCellValue(['x' => true], 'x', []));
        $this->assertSame(5, TALLKit::tableCellValue(['x' => 5], 'x', []));
        $this->assertSame(1.5, TALLKit::tableCellValue(['x' => 1.5], 'x', []));
    }

    public function test_cell_value_escapes_strings_and_keeps_html(): void
    {
        $this->assertSame('&lt;b&gt;a&lt;/b&gt;<br />'."\n".'b', TALLKit::tableCellValue(['x' => "<b>a</b>\nb"], 'x', []));
        $this->assertSame('<i>ok</i>', TALLKit::tableCellValue(['x' => new HtmlString('<i>ok</i>')], 'x', []));
    }

    public function test_cell_value_translates_only_when_asked(): void
    {
        app('translator')->addLines(['*.Active' => '<b>Ativo</b>'], 'en');

        $this->assertSame('<b>Ativo</b>', TALLKit::tableCellValue(['x' => 'Active'], 'x', ['translate' => true]));
        $this->assertSame('Active', TALLKit::tableCellValue(['x' => 'Active'], 'x', []));
        $this->assertSame('&lt;b&gt;', TALLKit::tableCellValue(['x' => '<b>'], 'x', ['translate' => true]));
    }

    public function test_cell_value_of_models(): void
    {
        $this->createUsers();
        $user = InteractsWithTableUser::find(1);

        $this->assertSame('Bob', TALLKit::tableCellValue(['author' => $user], 'author', []));
        $this->assertSame('Bob', TALLKit::tableCellValue(['team' => $user], 'team', []));
        $this->assertSame('Own', TALLKit::tableCellValue(['other' => new InteractsWithTableUser(['other' => 'Own'])], 'other', []));
        $this->assertSame('T', TALLKit::tableCellValue(['x' => new InteractsWithTableUser(['title' => 'T'])], 'x', []));
        $this->assertSame('Tx', TALLKit::tableCellValue(['x' => new InteractsWithTableUser(['text' => 'Tx'])], 'x', []));
        $this->assertSame('7', TALLKit::tableCellValue(['x' => new InteractsWithTableUser(['id' => 7])], 'x', []));

        $nested = (new InteractsWithTableUser(['name' => 'Outer']))->setRelation('x', $user);
        $this->assertSame('Outer', TALLKit::tableCellValue(['x' => $nested], 'x', []));

        $this->assertSame('Bob<br />Ann', TALLKit::tableCellValue(['users' => InteractsWithTableUser::whereKey([1, 2])->get()], 'users', []));
    }

    public function test_cell_value_of_dates(): void
    {
        $this->assertSame('10/15/2026 2:30 PM', TALLKit::tableCellValue(['x' => Carbon::parse('2026-10-15 14:30:00')], 'x', []));
        $this->assertSame('10/15/2026', TALLKit::tableCellValue(['x' => new \DateTimeImmutable('2026-10-15 00:00:00')], 'x', []));
        $this->assertSame('2:30 PM', TALLKit::tableCellValue(['x' => Carbon::parse('1970-01-01 14:30:00')], 'x', []));
    }

    public function test_cell_value_of_enums(): void
    {
        $this->assertSame('Is active', TALLKit::tableCellValue(['x' => InteractsWithTableStatus::Active], 'x', []));
        $this->assertSame('3', TALLKit::tableCellValue(['x' => InteractsWithTablePriority::High], 'x', []));
        $this->assertSame('Admin', TALLKit::tableCellValue(['x' => InteractsWithTableKind::Admin], 'x', []));
    }

    public function test_cell_value_of_objects(): void
    {
        $formattable = new class
        {
            public function format(): string
            {
                return '<1.00>';
            }
        };
        $needsArguments = new class
        {
            public $a = 'b';

            public function format(string $pattern): string
            {
                return $pattern;
            }
        };
        $arrayable = new class implements Arrayable
        {
            public function toArray(): array
            {
                return ['a', 'b'];
            }
        };
        $stringable = new class
        {
            public function __toString(): string
            {
                return 'S&S';
            }
        };

        $this->assertSame('&lt;1.00&gt;', TALLKit::tableCellValue(['x' => $formattable], 'x', []));
        $this->assertSame('{&quot;a&quot;:&quot;b&quot;}', TALLKit::tableCellValue(['x' => $needsArguments], 'x', []));
        $this->assertSame('a<br />b', TALLKit::tableCellValue(['x' => $arrayable], 'x', []));
        $this->assertSame('S&amp;S', TALLKit::tableCellValue(['x' => $stringable], 'x', []));
    }

    public function test_cell_value_of_lists(): void
    {
        $this->assertSame(
            'a<br /><br />Yes<br />No<br />1<br />2.5<br />{&quot;k&quot;:&quot;v/é&quot;}<br />[1,2]',
            TALLKit::tableCellValue(['x' => ['a', null, true, false, 1, 2.5, ['k' => 'v/é'], collect([1, 2])]], 'x', []),
        );
    }
}

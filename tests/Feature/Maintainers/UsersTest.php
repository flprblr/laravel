<?php

use App\Models\Permission as AppPermission;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Maatwebsite\Excel\Facades\Excel;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

it('authorizes index and orders by id asc by default', function () {
    $viewer = User::factory()->create();
    AppPermission::firstOrCreate(['name' => 'users.index'], ['guard_name' => 'web']);
    $viewer->givePermissionTo('users.index');
    actingAs($viewer);

    $u1 = User::factory()->create(['name' => 'Zed', 'email' => 'zed@example.com']);
    $u2 = User::factory()->create(['name' => 'Amy', 'email' => 'amy@example.com']);

    $response = get(route('maintainers.users.index'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('maintainers/users/Index')
            ->where('users.data.0.id', $viewer->id)
        );
});

it('filters users by name, email and id', function () {
    $viewer = User::factory()->create();
    AppPermission::firstOrCreate(['name' => 'users.index'], ['guard_name' => 'web']);
    $viewer->givePermissionTo('users.index');
    actingAs($viewer);

    $target = User::factory()->create(['name' => 'Juan Perez', 'email' => 'juan.perez@example.com']);
    User::factory()->create(['name' => 'Otro', 'email' => 'otro@example.com']);

    // by name
    get(route('maintainers.users.index', ['search' => 'Juan']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('users.data.0.id', $target->id));

    // by email
    get(route('maintainers.users.index', ['search' => 'juan.perez@']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('users.data.0.id', $target->id));

    // by id
    get(route('maintainers.users.index', ['search' => $target->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('users.data.0.id', $target->id));
});

it('updates password only when provided', function () {
    $actor = User::factory()->create();
    AppPermission::firstOrCreate(['name' => 'users.edit'], ['guard_name' => 'web']);
    $actor->givePermissionTo('users.edit');
    actingAs($actor);

    $user = User::factory()->create([
        'password' => Hash::make('OldPassword!123'),
    ]);

    // Without password
    patch(route('maintainers.users.update', $user), [
        'name' => 'Nuevo Nombre',
        'email' => $user->email,
    ])->assertRedirect();

    $user->refresh();
    expect(Hash::check('OldPassword!123', $user->password))->toBeTrue();

    // With password
    patch(route('maintainers.users.update', $user), [
        'name' => 'Nuevo Nombre 2',
        'email' => $user->email,
        'password' => 'NewPassword!123',
    ])->assertRedirect();

    $user->refresh();
    expect(Hash::check('NewPassword!123', $user->password))->toBeTrue();
});

it('authorizes export and import actions', function () {
    $actor = User::factory()->create();
    AppPermission::firstOrCreate(['name' => 'users.export'], ['guard_name' => 'web']);
    AppPermission::firstOrCreate(['name' => 'users.import'], ['guard_name' => 'web']);
    actingAs($actor);

    // Denied export
    get(route('maintainers.users.export'))->assertForbidden();

    // Allow export
    $actor->givePermissionTo('users.export');
    Excel::fake();
    get(route('maintainers.users.export'))->assertOk();

    // Denied import
    $file = UploadedFile::fake()->create('users.xlsx', 10, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    post(route('maintainers.users.import'), ['file' => $file])->assertForbidden();

    // Allow import
    $actor->givePermissionTo('users.import');
    Excel::fake();
    post(route('maintainers.users.import'), ['file' => $file])->assertRedirect(route('maintainers.users.import.form'));
});

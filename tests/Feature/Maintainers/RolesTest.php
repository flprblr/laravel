<?php

use App\Models\Permission as AppPermission;
use App\Models\Role;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Maatwebsite\Excel\Facades\Excel;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('authorizes roles index and orders by id asc by default', function () {
    $viewer = User::factory()->create();
    AppPermission::firstOrCreate(['name' => 'roles.index'], ['guard_name' => 'web']);
    $viewer->givePermissionTo('roles.index');
    actingAs($viewer);

    $r1 = Role::create(['name' => 'editor', 'guard_name' => 'web']);
    $r2 = Role::create(['name' => 'admin', 'guard_name' => 'web']);

    $response = get(route('maintainers.roles.index'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('maintainers/roles/Index'));
});

it('filters roles by name and id', function () {
    $viewer = User::factory()->create();
    AppPermission::firstOrCreate(['name' => 'roles.index'], ['guard_name' => 'web']);
    $viewer->givePermissionTo('roles.index');
    actingAs($viewer);

    $role = Role::create(['name' => 'manager', 'guard_name' => 'web']);
    Role::create(['name' => 'guest', 'guard_name' => 'web']);

    get(route('maintainers.roles.index', ['search' => 'manager']))->assertOk();
    get(route('maintainers.roles.index', ['search' => $role->id]))->assertOk();
});

it('authorizes roles export and import actions', function () {
    $actor = User::factory()->create();
    AppPermission::firstOrCreate(['name' => 'roles.export'], ['guard_name' => 'web']);
    AppPermission::firstOrCreate(['name' => 'roles.import'], ['guard_name' => 'web']);
    actingAs($actor);

    // Denied export
    get(route('maintainers.roles.export'))->assertForbidden();

    // Allow export
    $actor->givePermissionTo('roles.export');
    Excel::fake();
    get(route('maintainers.roles.export'))->assertOk();
});

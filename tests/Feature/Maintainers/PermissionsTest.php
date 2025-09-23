<?php

use App\Models\Permission;
use App\Models\Permission as AppPermissionModel;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Maatwebsite\Excel\Facades\Excel;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('authorizes permissions index and orders by id asc by default', function () {
    $viewer = User::factory()->create();
    AppPermissionModel::firstOrCreate(['name' => 'permissions.index'], ['guard_name' => 'web']);
    $viewer->givePermissionTo('permissions.index');
    actingAs($viewer);

    Permission::firstOrCreate(['name' => 'posts.create'], ['guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'posts.edit'], ['guard_name' => 'web']);

    $response = get(route('maintainers.permissions.index'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('maintainers/permissions/Index'));
});

it('filters permissions by name and id', function () {
    $viewer = User::factory()->create();
    AppPermissionModel::firstOrCreate(['name' => 'permissions.index'], ['guard_name' => 'web']);
    $viewer->givePermissionTo('permissions.index');
    actingAs($viewer);

    $perm = Permission::firstOrCreate(['name' => 'orders.view'], ['guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'orders.edit'], ['guard_name' => 'web']);

    get(route('maintainers.permissions.index', ['search' => 'orders.view']))->assertOk();
    get(route('maintainers.permissions.index', ['search' => $perm->id]))->assertOk();
});

it('authorizes permissions export and import actions', function () {
    $actor = User::factory()->create();
    AppPermissionModel::firstOrCreate(['name' => 'permissions.export'], ['guard_name' => 'web']);
    AppPermissionModel::firstOrCreate(['name' => 'permissions.import'], ['guard_name' => 'web']);
    actingAs($actor);

    // Denied export
    get(route('maintainers.permissions.export'))->assertForbidden();

    // Allow export
    $actor->givePermissionTo('permissions.export');
    Excel::fake();
    get(route('maintainers.permissions.export'))->assertOk();
});

<?php

namespace App\Actions\Roles;

use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DeleteRole
{
    public function handle(Role $role): void
    {
        $role->delete();

        // Clear the permission cache
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

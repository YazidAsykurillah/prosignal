<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DiscoveryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'discovery.run',
            'discovery.view',
            'discovery.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo(['discovery.view', 'discovery.manage']);
        }

        $member = Role::where('name', 'Member')->first();
        if ($member) {
            $member->givePermissionTo(['discovery.run', 'discovery.view']);
        }
    }
}

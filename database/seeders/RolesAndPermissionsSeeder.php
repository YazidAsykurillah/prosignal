<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Project
            'project.create',
            'project.view',
            'project.update',
            'project.delete',
            // Search
            'search.run',
            // Opportunity
            'opportunity.view',
            // Export
            'export.csv',
            // User Management
            'user.view',
            'user.manage',
            // Role Management
            'role.view',
            // Market Intelligence
            'market-intelligence.generate',
            'market-intelligence.view',
            'market-intelligence.update',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        // Super Admin — full access (bypasses all permission checks via Gate::before)
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        // Admin — system management (all except role management)
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::whereIn('name', [
            'project.create',
            'project.view',
            'project.update',
            'project.delete',
            'search.run',
            'opportunity.view',
            'export.csv',
            'user.view',
            'user.manage',
            'market-intelligence.generate',
            'market-intelligence.view',
            'market-intelligence.update',
        ])->get());

        // Member — application user
        $memberRole = Role::firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);
        $memberRole->syncPermissions(Permission::whereIn('name', [
            'project.create',
            'project.view',
            'project.update',
            'project.delete',
            'search.run',
            'opportunity.view',
            'export.csv',
            'market-intelligence.generate',
            'market-intelligence.view',
            'market-intelligence.update',
        ])->get());
    }
}

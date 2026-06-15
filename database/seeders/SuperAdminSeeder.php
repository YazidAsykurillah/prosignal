<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'superadmin@prosignal.local'],
            [
                'name' => 'Super Admin',
                'password' => 'password', // Development only — change in production
            ]
        );

        $user->assignRole('Super Admin');

        activity('auth')
            ->performedOn($user)
            ->causedBy($user)
            ->log('Super Admin Created');
    }
}

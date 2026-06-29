<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'fred.osei@nmc.gov.gh'],
            [
                'name' => 'Fred Osei',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $user->assignRole($role);

        $this->command->info('Super admin user created: fred.osei@nmc.gov.gh / password');
    }
}

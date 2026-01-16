<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing admin role (created by RoleSeeder)
        $adminRole = Role::where('name', 'Admin')->first();

        if (!$adminRole) {
            $this->command->error('Admin role not found! Run RoleSeeder first.');
            return;
        }

        // Get first branch
        $branch = \App\Models\Branch::first();

        if (!$branch) {
            $this->command->error('No branches found! Run BranchSeeder first.');
            return;
        }

        // Check if admin user already exists
        $existingUser = User::where('username', 'admin')->first();

        if ($existingUser) {
            $this->command->warn('Admin user already exists. Skipping...');
            return;
        }

        // Create test admin user
        $user = User::create([
            'username' => 'admin',
            'name' => 'System Administrator',
            'email' => 'admin@guysiri.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);

        $this->command->info("✅ Test admin user created:");
        $this->command->info("   Username: admin");
        $this->command->info("   Password: password");
        $this->command->info("   Email: admin@guysiri.com");
    }
}

<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Runs all required seeders for GUYSIRI CLINIC Management System
     */
    public function run(): void
    {
        // Run seeders in correct order
        $this->call([
            RoleSeeder::class,          // 1. Create roles first
            BranchSeeder::class,        // 2. Create branches
            PermissionSeeder::class,    // 3. Create permissions
            TestUserSeeder::class,      // 4. Create default admin user
        ]);

        $this->command->info('✅ All seeders completed successfully!');
        $this->command->info('   - Roles: 5 created');
        $this->command->info('   - Branches: 2 created');
        $this->command->info('   - Permissions: 70+ created');
        $this->command->info('   - Admin User: 1 created (username: admin, password: password)');
    }
}

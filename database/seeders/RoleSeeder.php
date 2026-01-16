<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // System Roles for GUYSIRI CLINIC Management System
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'System Administrator - Full access to all features',
                'is_system' => true,
            ],
            [
                'name' => 'Manager',
                'description' => 'Branch Manager - Manage branch operations and reports',
                'is_system' => true,
            ],
            [
                'name' => 'PT',
                'description' => 'Physical Therapist - Provide treatments and manage patient care',
                'is_system' => true,
            ],
            [
                'name' => 'Receptionist',
                'description' => 'Receptionist - Handle appointments, queue, and patient registration',
                'is_system' => true,
            ],
            [
                'name' => 'Accountant',
                'description' => 'Accountant - Manage billing, invoices, and financial reports',
                'is_system' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}

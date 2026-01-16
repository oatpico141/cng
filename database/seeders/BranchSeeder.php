<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::firstOrCreate(
            ['code' => 'MAIN'],
            [
                'name' => 'Main Branch',
                'address' => '123 Sukhumvit Road, Bangkok 10110',
                'phone' => '021234567',
                'email' => 'main@guysiri.com',
                'is_active' => true,
                'settings' => null
            ]
        );

        Branch::firstOrCreate(
            ['code' => 'SILOM'],
            [
                'name' => 'Silom Branch',
                'address' => '456 Silom Road, Bangkok 10500',
                'phone' => '021234568',
                'email' => 'silom@guysiri.com',
                'is_active' => true,
                'settings' => null
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;

class TestPatientSeeder extends Seeder
{
    public function run(): void
    {
        // Get Main Branch ID
        $mainBranch = \App\Models\Branch::where('code', 'MAIN')->first();

        $patient = Patient::create([
            'phone' => '0812345678',
            'name' => 'John Doe Test',
            'email' => 'john.doe@test.com',
            'date_of_birth' => '1990-01-15',
            'gender' => 'male',
            'address' => '123 Test Street, Bangkok 10110',
            'emergency_contact' => '0987654321',
            'emergency_name' => 'Jane Doe',
            'notes' => 'Test patient created for PM verification - Phase 2.4',
            'first_visit_branch_id' => $mainBranch->id
        ]);

        echo "✅ Patient created successfully!\n";
        echo "ID: {$patient->id}\n";
        echo "Phone: {$patient->phone}\n";
        echo "Name: {$patient->name}\n";
        echo "Email: {$patient->email}\n";
        echo "Branch: {$patient->firstVisitBranch->name}\n";
    }
}

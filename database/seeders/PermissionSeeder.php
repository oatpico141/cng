<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 45+ permissions for GUYSIRI CLINIC Management System
     */
    public function run(): void
    {
        $permissions = [
            // Patients Module (5 permissions)
            ['module' => 'patients', 'action' => 'create', 'description' => 'Create new patient records'],
            ['module' => 'patients', 'action' => 'read', 'description' => 'View patient information'],
            ['module' => 'patients', 'action' => 'update', 'description' => 'Edit patient information'],
            ['module' => 'patients', 'action' => 'delete', 'description' => 'Delete patient records'],
            ['module' => 'patients', 'action' => 'view_history', 'description' => 'View patient treatment history'],

            // Appointments Module (6 permissions)
            ['module' => 'appointments', 'action' => 'create', 'description' => 'Create new appointments'],
            ['module' => 'appointments', 'action' => 'read', 'description' => 'View appointments'],
            ['module' => 'appointments', 'action' => 'update', 'description' => 'Edit appointments'],
            ['module' => 'appointments', 'action' => 'delete', 'description' => 'Cancel appointments'],
            ['module' => 'appointments', 'action' => 'confirm', 'description' => 'Confirm appointments'],
            ['module' => 'appointments', 'action' => 'reschedule', 'description' => 'Reschedule appointments'],

            // Queue Module (4 permissions)
            ['module' => 'queue', 'action' => 'view', 'description' => 'View queue dashboard'],
            ['module' => 'queue', 'action' => 'manage', 'description' => 'Manage queue order'],
            ['module' => 'queue', 'action' => 'start_treatment', 'description' => 'Start treatment session'],
            ['module' => 'queue', 'action' => 'end_treatment', 'description' => 'End treatment session'],

            // Treatments Module (5 permissions)
            ['module' => 'treatments', 'action' => 'create', 'description' => 'Create treatment records'],
            ['module' => 'treatments', 'action' => 'read', 'description' => 'View treatment records'],
            ['module' => 'treatments', 'action' => 'update', 'description' => 'Edit treatment records'],
            ['module' => 'treatments', 'action' => 'delete', 'description' => 'Delete treatment records'],
            ['module' => 'treatments', 'action' => 'approve', 'description' => 'Approve treatment records'],

            // Billing Module (6 permissions)
            ['module' => 'billing', 'action' => 'create_invoice', 'description' => 'Create invoices'],
            ['module' => 'billing', 'action' => 'view_invoice', 'description' => 'View invoices'],
            ['module' => 'billing', 'action' => 'approve_invoice', 'description' => 'Approve invoices'],
            ['module' => 'billing', 'action' => 'process_payment', 'description' => 'Process payments'],
            ['module' => 'billing', 'action' => 'void_invoice', 'description' => 'Void invoices'],
            ['module' => 'billing', 'action' => 'refund', 'description' => 'Process refunds'],

            // Course Packages Module (5 permissions)
            ['module' => 'courses', 'action' => 'purchase', 'description' => 'Purchase course packages'],
            ['module' => 'courses', 'action' => 'view', 'description' => 'View course packages'],
            ['module' => 'courses', 'action' => 'cancel', 'description' => 'Cancel course packages'],
            ['module' => 'courses', 'action' => 'renew', 'description' => 'Renew course packages'],
            ['module' => 'courses', 'action' => 'manage', 'description' => 'Manage course package definitions'],

            // Payments Module (4 permissions)
            ['module' => 'payments', 'action' => 'create', 'description' => 'Record payments'],
            ['module' => 'payments', 'action' => 'view', 'description' => 'View payment records'],
            ['module' => 'payments', 'action' => 'approve', 'description' => 'Approve payments'],
            ['module' => 'payments', 'action' => 'refund', 'description' => 'Process payment refunds'],

            // Reports Module (5 permissions)
            ['module' => 'reports', 'action' => 'view_dashboard', 'description' => 'View KPI dashboard'],
            ['module' => 'reports', 'action' => 'view_pl', 'description' => 'View P&L reports'],
            ['module' => 'reports', 'action' => 'view_revenue', 'description' => 'View revenue reports'],
            ['module' => 'reports', 'action' => 'view_utilization', 'description' => 'View utilization reports'],
            ['module' => 'reports', 'action' => 'export', 'description' => 'Export reports'],

            // Staff Module (5 permissions)
            ['module' => 'staff', 'action' => 'create', 'description' => 'Create staff accounts'],
            ['module' => 'staff', 'action' => 'read', 'description' => 'View staff information'],
            ['module' => 'staff', 'action' => 'update', 'description' => 'Edit staff information'],
            ['module' => 'staff', 'action' => 'delete', 'description' => 'Delete staff accounts'],
            ['module' => 'staff', 'action' => 'manage_roles', 'description' => 'Assign roles to staff'],

            // Branches Module (4 permissions)
            ['module' => 'branches', 'action' => 'create', 'description' => 'Create branches'],
            ['module' => 'branches', 'action' => 'read', 'description' => 'View branch information'],
            ['module' => 'branches', 'action' => 'update', 'description' => 'Edit branch information'],
            ['module' => 'branches', 'action' => 'manage_settings', 'description' => 'Manage branch settings'],

            // Inventory/Stock Module (5 permissions)
            ['module' => 'inventory', 'action' => 'view', 'description' => 'View inventory'],
            ['module' => 'inventory', 'action' => 'manage', 'description' => 'Manage inventory items'],
            ['module' => 'inventory', 'action' => 'stock_in', 'description' => 'Record stock in transactions'],
            ['module' => 'inventory', 'action' => 'stock_out', 'description' => 'Record stock out transactions'],
            ['module' => 'inventory', 'action' => 'adjust', 'description' => 'Adjust inventory quantities'],

            // Equipment Module (4 permissions)
            ['module' => 'equipment', 'action' => 'view', 'description' => 'View equipment registry'],
            ['module' => 'equipment', 'action' => 'manage', 'description' => 'Manage equipment'],
            ['module' => 'equipment', 'action' => 'maintenance', 'description' => 'Record equipment maintenance'],
            ['module' => 'equipment', 'action' => 'calibration', 'description' => 'Record equipment calibration'],

            // Commissions Module (5 permissions)
            ['module' => 'commissions', 'action' => 'view', 'description' => 'View commission records'],
            ['module' => 'commissions', 'action' => 'calculate', 'description' => 'Calculate commissions'],
            ['module' => 'commissions', 'action' => 'approve', 'description' => 'Approve commission payments'],
            ['module' => 'commissions', 'action' => 'pay', 'description' => 'Process commission payments'],
            ['module' => 'commissions', 'action' => 'clawback', 'description' => 'Process commission clawbacks'],

            // Refunds Module (3 permissions)
            ['module' => 'refunds', 'action' => 'create', 'description' => 'Create refund requests'],
            ['module' => 'refunds', 'action' => 'approve', 'description' => 'Approve refunds'],
            ['module' => 'refunds', 'action' => 'process', 'description' => 'Process refunds'],

            // Settings/System Module (4 permissions)
            ['module' => 'system', 'action' => 'manage_settings', 'description' => 'Manage system settings'],
            ['module' => 'system', 'action' => 'view_logs', 'description' => 'View audit logs'],
            ['module' => 'system', 'action' => 'manage_roles', 'description' => 'Manage roles'],
            ['module' => 'system', 'action' => 'manage_permissions', 'description' => 'Manage permissions'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                [
                    'module' => $permissionData['module'],
                    'action' => $permissionData['action']
                ],
                ['description' => $permissionData['description']]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ==========================================
        // 1. DEFINE PERMISSIONS
        // ==========================================
        $permissions = [
            'view dashboard',
            'view branches',
            'create branches',
            'edit branches',
            'delete branches',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view stock',
            'manage stock',
            'view transactions',
            'create transactions',
            'print transactions',
            'cancel transactions',
            'view reports',
            'export reports',
            'print reports',
            'view users',
            'create users',
            'edit users',
            'delete users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ==========================================
        // 2. CREATE ROLES (Hanya jika belum ada)
        // ==========================================
        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $supervisorRole = Role::firstOrCreate(['name' => 'supervisor']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);
        $warehouseRole = Role::firstOrCreate(['name' => 'warehouse']);

        // ==========================================
        // 3. ASSIGN PERMISSIONS TO ROLES
        // ==========================================
        $ownerRole->syncPermissions(Permission::all());

        $managerRole->syncPermissions([
            'view dashboard',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view stock',
            'manage stock',
            'view transactions',
            'create transactions',
            'print transactions',
            'cancel transactions',
            'view reports',
            'export reports',
            'print reports',
            'view users',
        ]);

        $supervisorRole->syncPermissions([
            'view dashboard',
            'view products',
            'view stock',
            'view transactions',
            'print transactions',
            'view reports',
        ]);

        $cashierRole->syncPermissions([
            'view dashboard',
            'view products',
            'create transactions',
            'print transactions',
        ]);

        $warehouseRole->syncPermissions([
            'view dashboard',
            'view products',
            'view stock',
            'manage stock',
        ]);

        // ==========================================
        // 4. CREATE BRANCHES (Hanya jika belum ada)
        // ==========================================
        $branchesData = [
            [
                'code' => 'JKT01',
                'name' => 'Mini Market Jayusman Jakarta',
                'city' => 'Jakarta',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'phone' => '021-1234567'
            ],
            [
                'code' => 'BDG01',
                'name' => 'Mini Market Jayusman Bandung',
                'city' => 'Bandung',
                'address' => 'Jl. Asia Afrika No. 45, Bandung',
                'phone' => '022-7654321'
            ],
            [
                'code' => 'SBY01',
                'name' => 'Mini Market Jayusman Surabaya',
                'city' => 'Surabaya',
                'address' => 'Jl. Tunjungan No. 78, Surabaya',
                'phone' => '031-9876543'
            ],
            [
                'code' => 'SMG01',
                'name' => 'Mini Market Jayusman Semarang',
                'city' => 'Semarang',
                'address' => 'Jl. Pandanaran No. 56, Semarang',
                'phone' => '024-5551234'
            ],
            [
                'code' => 'YOG01',
                'name' => 'Mini Market Jayusman Yogyakarta',
                'city' => 'Yogyakarta',
                'address' => 'Jl. Malioboro No. 90, Yogyakarta',
                'phone' => '0274-333555'
            ],
        ];

        foreach ($branchesData as $branchData) {
            Branch::firstOrCreate(['code' => $branchData['code']], $branchData);
        }

        $branches = Branch::all();

        // ==========================================
        // 5. CREATE USERS WITH ROLES
        // ==========================================
        $defaultPassword = 'password123';
        $mustChangePassword = true;

        // Owner (tidak terikat cabang)
        $owner = User::firstOrCreate(
            ['email' => 'owner@minimarket.com'],
            [
                'name' => 'Pak Jayusman',
                'password' => Hash::make($defaultPassword),
                'branch_id' => null,
                'email_verified_at' => now(),
                'must_change_password' => $mustChangePassword,
            ]
        );
        $owner->assignRole('owner');

        // Manager untuk setiap cabang
        foreach ($branches as $branch) {
            $manager = User::firstOrCreate(
                ['email' => "manager.{$branch->code}@minimarket.com"],
                [
                    'name' => "Manager {$branch->name}",
                    'password' => Hash::make($defaultPassword),
                    'branch_id' => $branch->id,
                    'email_verified_at' => now(),
                    'must_change_password' => $mustChangePassword,
                ]
            );
            $manager->assignRole('manager');
        }

        // Supervisor untuk setiap cabang
        foreach ($branches as $branch) {
            $supervisor = User::firstOrCreate(
                ['email' => "supervisor.{$branch->code}@minimarket.com"],
                [
                    'name' => "Supervisor {$branch->name}",
                    'password' => Hash::make($defaultPassword),
                    'branch_id' => $branch->id,
                    'email_verified_at' => now(),
                    'must_change_password' => $mustChangePassword,
                ]
            );
            $supervisor->assignRole('supervisor');
        }

        // Cashier (2 orang per cabang)
        foreach ($branches as $branch) {
            for ($i = 1; $i <= 2; $i++) {
                $cashier = User::firstOrCreate(
                    ['email' => "cashier{$i}.{$branch->code}@minimarket.com"],
                    [
                        'name' => "Cashier {$i} {$branch->name}",
                        'password' => Hash::make($defaultPassword),
                        'branch_id' => $branch->id,
                        'email_verified_at' => now(),
                        'must_change_password' => $mustChangePassword,
                    ]
                );
                $cashier->assignRole('cashier');
            }
        }

        // Warehouse Staff per cabang
        foreach ($branches as $branch) {
            $warehouse = User::firstOrCreate(
                ['email' => "warehouse.{$branch->code}@minimarket.com"],
                [
                    'name' => "Warehouse {$branch->name}",
                    'password' => Hash::make($defaultPassword),
                    'branch_id' => $branch->id,
                    'email_verified_at' => now(),
                    'must_change_password' => $mustChangePassword,
                ]
            );
            $warehouse->assignRole('warehouse');
        }
    }
}

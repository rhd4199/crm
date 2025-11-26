<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat super admin (penyedia layanan)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@crm.test'],
            [
                'name'        => 'Super Admin',
                'password'    => Hash::make('password'), // ganti nanti
                'global_role' => 'super_admin',
                'company_id'  => null,
                'company_role'=> null,
                'is_active'   => true,
            ]
        );

        // 2. Buat 1 company contoh
        $company = Company::firstOrCreate(
            ['code' => 'DEPATI'],
            [
                'name'    => 'Depati Akademi',
                'address' => 'Jambi',
                'phone'   => '08123456789',
                'email'   => 'info@depati.test',
                'status'  => 'active',
            ]
        );

        // 3. Buat admin untuk company tersebut
        $companyAdmin = User::firstOrCreate(
            ['email' => 'admin@depati.test'],
            [
                'name'        => 'Admin Depati',
                'password'    => Hash::make('password'), // ganti nanti
                'global_role' => 'user',
                'company_id'  => $company->id,
                'company_role'=> 'admin',
                'is_active'   => true,
            ]
        );

        // 4. Buat default pipeline stages untuk company ini
        $stages = [
            [
                'name'       => 'Interest - Low',
                'type'       => 'open',
                'sort_order' => 1,
            ],
            [
                'name'       => 'Interest - Medium',
                'type'       => 'open',
                'sort_order' => 2,
            ],
            [
                'name'       => 'Interest - High',
                'type'       => 'open',
                'sort_order' => 3,
            ],
            [
                'name'       => 'Contacted',
                'type'       => 'open',
                'sort_order' => 4,
            ],
            [
                'name'       => 'Hold',
                'type'       => 'hold',
                'sort_order' => 5,
            ],
            [
                'name'       => 'Closed Won',
                'type'       => 'won',
                'sort_order' => 6,
            ],
            [
                'name'       => 'Closed Lost',
                'type'       => 'lost',
                'sort_order' => 7,
            ],
        ];

        foreach ($stages as $index => $stageData) {
            PipelineStage::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'name'       => $stageData['name'],
                ],
                [
                    'type'       => $stageData['type'],
                    'sort_order' => $stageData['sort_order'],
                    'is_default' => $index === 0, // stage pertama jadi default
                ]
            );
        }

        $this->command->info('Initial setup seeding completed.');
        $this->command->info('Super Admin login: superadmin@crm.test / password');
        $this->command->info('Company Admin login: admin@depati.test / password');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'roleID' => 1,
                'roleName' => 'Admin',
                'description' => 'Register and Monitor the Document',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'roleID' => 2,
                'roleName' => 'Staff',
                'description' => 'Responsible for receiving and Processing of Document',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Use upsert to avoid duplicates on re-seeding
        DB::table('roles')->upsert($roles, ['roleID'], ['roleName', 'description', 'updated_at']);
    }
}
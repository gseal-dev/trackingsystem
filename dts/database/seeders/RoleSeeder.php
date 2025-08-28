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
                'roleName' => 'DocumentOwner',
                'description' => 'Owner of the Document',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'roleID' => 3,
                'roleName' => 'Staff',
                'description' => 'Responsible for receiving and Processing of Document',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'roleID' => 4,
                'roleName' => 'Auditor',
                'description' => 'Tracking of Documents and Generating Reports',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Use upsert to avoid duplicates on re-seeding
        DB::table('roles')->upsert($roles, ['roleID'], ['roleName', 'description', 'updated_at']);
    }
}
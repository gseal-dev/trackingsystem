<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'depID' => 1,
                'depName' => 'Admin',
                'description' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Use upsert to avoid duplicates on re-seeding
        DB::table('departments')->upsert($departments, ['depID'], ['depName', 'description', 'updated_at']);
    }
}
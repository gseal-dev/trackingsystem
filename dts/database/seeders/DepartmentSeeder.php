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
            [
                'depID' => 2,
                'depName' => 'Office of the Chancellor',
                'description' => 'Office of the Chancellor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'depID' => 3,
                'depName' => 'CSBO',
                'description' => 'Campus Student Body Organization',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'depID' => 4,
                'depName' => 'SAS',
                'description' => 'Student Affairs and Services',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'depID' => 5,
                'depName' => 'COT',
                'description' => 'College of Technology',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'depID' => 6,
                'depName' => 'CIT',
                'description' => 'College of Information Technology',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'depID' => 7,
                'depName' => 'COM',
                'description' => 'College of Management',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'depID' => 8,
                'depName' => 'COE',
                'description' => 'College of Engineering',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'depID' => 9,
                'depName' => 'CE',
                'description' => 'College of Education',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'depID' => 10,
                'depName' => 'ICJE',
                'description' => 'Institute of Crimial Justice Education',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'depID' => 11,
                'depName' => 'CAS',
                'description' => 'College of Arts and Sciences',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Use upsert to avoid duplicates on re-seeding
        DB::table('departments')->upsert($departments, ['depID'], ['depName', 'description', 'updated_at']);
    }
}
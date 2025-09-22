<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['username' => 'Admin'],
            [
                'email' => 'admin@mail.com',
                'password' => '$2y$12$v7k/WCqgT2gFG4dkMEAteON5SvVKj0A4dcTPvpsUGzA.msD7yfll2',
                'firstName' => '',
                'middleName' => '',
                'lastName' => '',
                'roleID' => 1,
                'departmentID' => 1,
                'phoneNo' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
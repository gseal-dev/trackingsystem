<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed lookup tables first (order matters due to foreign keys)
        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            DocumentStatusSeeder::class,
        ]);

        // Optionally create test users after lookup tables are populated
        // Uncomment the lines below if you want to create test users
        
        // \App\Models\User::factory()->create([
        //     'username' => 'admin',
        //     'email' => 'admin@example.com',
        //     'firstName' => 'Admin',
        //     'lastName' => 'User',
        //     'roleID' => 1, // Admin role
        //     'departmentID' => 1, // CIT department
        // ]);

        // \App\Models\User::factory()->create([
        //     'username' => 'testuser',
        //     'email' => 'test@example.com',
        //     'firstName' => 'Test',
        //     'lastName' => 'User',
        //     'roleID' => 2, // Document Owner role
        //     'departmentID' => 1, // CIT department
        // ]);
    }
}
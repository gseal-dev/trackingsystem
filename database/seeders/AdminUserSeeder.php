<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Intentionally left empty: no default admin is seeded on deploy.
        // The first admin account is created through the /setup flow instead.
    }
}
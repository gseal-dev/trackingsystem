<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'statusID' => 1,
                'statusName' => 'Pending',
                'description' => 'Document is pending review',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'statusID' => 2,
                'statusName' => 'Approved',
                'description' => 'Document has been approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'statusID' => 3,
                'statusName' => 'Rejected',
                'description' => 'Document has been rejected',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'statusID' => 4,
                'statusName' => 'In Review',
                'description' => 'Document is currently being reviewed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Use upsert to avoid duplicates on re-seeding
        DB::table('document_statuses')->upsert($statuses, ['statusID'], ['statusName', 'description', 'updated_at']);
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'middleName')) {
                $table->string('middleName')->nullable();
            }

            if (! Schema::hasColumn('users', 'phoneNo')) {
                $table->string('phoneNo')->nullable();
            }

            if (! Schema::hasColumn('users', 'departmentID')) {
                $table->unsignedBigInteger('departmentID')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'middleName')) {
                $table->dropColumn('middleName');
            }

            if (Schema::hasColumn('users', 'phoneNo')) {
                $table->dropColumn('phoneNo');
            }

            if (Schema::hasColumn('users', 'departmentID')) {
                $table->dropColumn('departmentID');
            }
        });
    }
};
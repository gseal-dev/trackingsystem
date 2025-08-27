<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('filePath')->nullable()->after('currentStatus');
            $table->string('googleDriveId')->nullable()->after('filePath');
            $table->timestamps(); // Add Laravel's standard timestamps
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['filePath', 'googleDriveId', 'created_at', 'updated_at']);
        });
    }
};
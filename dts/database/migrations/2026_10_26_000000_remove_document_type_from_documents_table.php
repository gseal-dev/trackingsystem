<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades.Schema;

/**
 * Remove the document type field entirely.
 */
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'documentType')) {
                $table->dropColumn('documentType');
            }
        });
    }

    public function down(): void
    {
        // Not reversible: document type values cannot be restored.
    }
};

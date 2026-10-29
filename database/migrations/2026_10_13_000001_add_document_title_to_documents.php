<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the "Document Title" entered when uploading a document.
 * (The existing "title" column keeps holding the Subject.)
 * Documents uploaded before this change have no document title.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('documents', 'documentTitle')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('documentTitle')->nullable()->after('documentNo');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('documents', 'documentTitle')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropColumn('documentTitle');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Documents no longer have a status (Pending / Approved / ...).
 * Removes documents.currentStatus, document_histories.statusID and the document_statuses table.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->dropColumnWithForeignKey('documents', 'currentStatus');
        $this->dropColumnWithForeignKey('document_histories', 'statusID');

        Schema::dropIfExists('document_statuses');
    }

    public function down(): void
    {
        // Not reversible: the old status values cannot be restored.
    }

    private function dropColumnWithForeignKey(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        foreach (Schema::getForeignKeys($table) as $foreignKey) {
            $columns = array_map('strtolower', $foreignKey['columns']);

            if (in_array(strtolower($column), $columns, true)) {
                Schema::table($table, function (Blueprint $t) use ($foreignKey, $column, $driver) {
                    // SQLite has unnamed foreign keys, so address them by column there
                    $t->dropForeign($driver === 'sqlite' || empty($foreignKey['name']) ? [$column] : $foreignKey['name']);
                });
            }
        }

        Schema::table($table, function (Blueprint $t) use ($column) {
            $t->dropColumn($column);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_histories', function (Blueprint $table) {
            $table->id('historyID');
            $table->unsignedBigInteger('userID')->nullable(); // who performed the action (null when unknown)
            $table->string('ip', 45)->nullable();             // source IP (IPv4 or IPv6)
            $table->string('action');                         // login, login_failed, lock, unlock, password_reset, role_change
            $table->text('details')->nullable();              // extra context (target user, changed field, etc.)
            $table->timestamps();

            $table->foreign('userID')->references('userID')->on('users')->onDelete('set null');
            $table->index(['action', 'created_at']);
            $table->index('userID');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_histories');
    }
};

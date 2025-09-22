<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->string('documentId')->primary(); 
            $table->string('documentNo')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('documentType');
            $table->unsignedBigInteger('ownerID');
            $table->unsignedBigInteger('currentStatus');
            $table->unsignedBigInteger('currentDepartmentID')->default(1);
            $table->string('filePath')->nullable(); 
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('ownerID')->references('userID')->on('users');
            $table->foreign('currentStatus')->references('statusID')->on('document_statuses');
            $table->foreign('currentDepartmentID')->references('depID')->on('departments');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
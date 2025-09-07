<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_histories', function (Blueprint $table) {
            $table->id('historyID');
            $table->string('documentId'); 
            $table->unsignedBigInteger('prevDepartmentID')->nullable();
            $table->unsignedBigInteger('currentDepartmentID');
            $table->unsignedBigInteger('statusID');
            $table->unsignedBigInteger('userID');
            $table->text('action')->nullable();
            $table->timestamps();

            $table->foreign('documentId')->references('documentId')->on('documents')->onDelete('cascade');
            $table->foreign('prevDepartmentID')->references('depID')->on('departments');
            $table->foreign('currentDepartmentID')->references('depID')->on('departments');
            $table->foreign('statusID')->references('statusID')->on('document_statuses');
            $table->foreign('userID')->references('userID')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_histories');
    }
};
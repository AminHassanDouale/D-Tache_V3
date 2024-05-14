<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('assigned_id')->nullable();
            $table->string('tags')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->foreignId('status_id')->nullable()->constrained('statuses'); 
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('priority_id')->constrained('priorities')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

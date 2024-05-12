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
            $table->id()->primary();
            $table->string('name');
            $table->text('description');
            $table->integer('assigned_id');
            $table->string('tags')->nullable();
            $table->date('start_date');
            $table->date('due_date');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('status_id')->constrained('statuses');
          //  $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('priority_id')->constrained('priorities');
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

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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('pin_code');
            $table->string('city');
            $table->string('state');
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('studentId')->unique();
            $table->date('join_date');
            $table->date('birth_date');
            $table->bigInteger('total_amount')->default('350000');
            $table->unsignedBigInteger('billmethod_id');
            $table->unsignedBigInteger('status_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

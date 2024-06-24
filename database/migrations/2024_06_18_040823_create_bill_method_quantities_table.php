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
        Schema::create('bill_method_quantities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_method_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->decimal('remaining', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_method_quantities');
    }
};

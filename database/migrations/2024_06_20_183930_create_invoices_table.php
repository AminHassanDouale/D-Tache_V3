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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('invoiceId')->unique();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('bill_method_quantities_id')->constrained('bill_method_quantities')->onDelete('cascade');
            $table->string('student_name');
            $table->string('studentId');
            $table->string('subject');
            $table->date('start_date');
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('status_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

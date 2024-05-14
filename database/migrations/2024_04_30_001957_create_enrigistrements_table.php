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
        Schema::create('enrigistrements', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('adresse');
            $table->date('dob');
            $table->string('msisdn');
            $table->string('gender');
            $table->string('numero_piece_identite');
            $table->string('personne_contact');
            $table->string('personne_contact_tel');
            $table->string('attachment_photo');
            $table->string('attachment_identite');
            $table->string('attachment_formulaire');
            $table->foreignId('user_id')->constrained();
            //$table->foreignId('department_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrigistrements');
    }
};

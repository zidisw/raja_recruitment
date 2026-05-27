<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nik', 16)->unique();
            $table->string('place_of_birth');
            $table->date('date_of_birth');
            $table->string('gender');
            $table->string('religion');
            $table->string('marital_status');
            $table->text('address_ktp');
            $table->text('address_domicile');
            $table->string('whatsapp');
            $table->string('linkedin_url')->nullable();

            // Lampiran Dokumen
            $table->string('ktp_path')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('portfolio_path')->nullable();
            $table->string('certificate_path')->nullable();
            $table->string('paklaring_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_profiles');
    }
};

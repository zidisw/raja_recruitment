<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('degree'); // SMA, D3, S1, S2, dll
            $table->string('institution_name');
            $table->string('major');
            $table->year('start_year');
            $table->year('end_year')->nullable();
            $table->string('gpa', 5)->nullable(); // IPK / Nilai Akhir
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_education');
    }
};

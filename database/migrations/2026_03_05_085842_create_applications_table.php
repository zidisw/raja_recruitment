<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Kandidat yang melamar
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->integer('status')->default(0); // Enum: ApplicationStatus
            $table->text('hr_notes')->nullable();
            $table->timestamps();
            
            // Mencegah user melamar posisi yang sama berkali-kali
            $table->unique(['user_id', 'job_id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
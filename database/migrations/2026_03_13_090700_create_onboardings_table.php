<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboardings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->date('joining_date');
            $table->string('onboarding_status')->default('pending');
            $table->timestamps();

            $table->unique('application_id');
            $table->index('onboarding_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboardings');
    }
};

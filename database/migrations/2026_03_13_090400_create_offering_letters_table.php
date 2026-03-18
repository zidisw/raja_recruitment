<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offering_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->date('offer_date');
            $table->string('status')->default('waiting_response');
            $table->timestamps();

            $table->unique('application_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offering_letters');
    }
};

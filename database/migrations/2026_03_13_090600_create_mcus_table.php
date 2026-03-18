<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->date('mcu_date');
            $table->string('result')->default('fit');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('application_id');
            $table->index('result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcus');
    }
};

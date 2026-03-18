<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ptk', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_ptk')->unique();
            $table->string('department');
            $table->string('posisi');
            $table->unsignedInteger('jumlah_kebutuhan')->default(1);
            $table->text('alasan_permintaan')->nullable();
            $table->date('tanggal_permintaan');
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->index(['status', 'tanggal_permintaan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ptk');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_stage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->integer('stage'); // ApplicationStatus value
            $table->enum('decision', ['passed', 'rejected']);
            $table->text('notes');
            $table->foreignId('decided_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index('application_id');
            $table->index('stage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_stage_logs');
    }
};

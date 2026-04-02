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
        Schema::table('offering_letters', function (Blueprint $table) {
            $table->string('signed_file_path')->nullable()->after('file_path');
            $table->timestamp('signed_at')->nullable()->after('signed_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offering_letters', function (Blueprint $table) {
            $table->dropColumn(['signed_file_path', 'signed_at']);
        });
    }
};

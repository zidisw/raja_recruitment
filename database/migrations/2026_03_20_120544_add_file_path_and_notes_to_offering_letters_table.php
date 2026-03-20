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
            $table->string('file_path')->nullable()->after('offer_date');
            $table->text('candidate_notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offering_letters', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'candidate_notes']);
        });
    }
};

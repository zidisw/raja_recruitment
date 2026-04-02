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
        Schema::table('onboardings', function (Blueprint $table) {
            $table->date('onsite_date')->nullable()->after('joining_date');
            $table->string('onsite_location')->nullable()->after('onsite_date');
            $table->text('onsite_notes')->nullable()->after('onsite_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboardings', function (Blueprint $table) {
            $table->dropColumn(['onsite_date', 'onsite_location', 'onsite_notes']);
        });
    }
};

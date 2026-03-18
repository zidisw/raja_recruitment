<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (! Schema::hasColumn('jobs', 'benefits')) {
                $table->text('benefits')->nullable()->after('requirements');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (Schema::hasColumn('jobs', 'benefits')) {
                $table->dropColumn('benefits');
            }
        });
    }
};

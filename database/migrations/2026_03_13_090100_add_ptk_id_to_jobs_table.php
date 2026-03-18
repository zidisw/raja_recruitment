<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (! Schema::hasColumn('jobs', 'ptk_id')) {
                $table->foreignId('ptk_id')->nullable()->after('site_id')->constrained('ptk')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (Schema::hasColumn('jobs', 'ptk_id')) {
                $table->dropForeign(['ptk_id']);
                $table->dropColumn('ptk_id');
            }
        });
    }
};

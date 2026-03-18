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
        Schema::table('jobs', function (Blueprint $table) {
            // department_id: add column+FK only if column doesn't already exist
            // (may exist from a partial earlier migration run on the live DB)
            if (! Schema::hasColumn('jobs', 'department_id')) {
                $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            }

            // site_id: add column+FK if column missing, otherwise just add the FK
            if (! Schema::hasColumn('jobs', 'site_id')) {
                $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            } else {
                // Column already exists from the partial run — add only the FK constraint
                $table->foreign('site_id')->references('id')->on('sites')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['department_id', 'site_id']);
        });
    }
};

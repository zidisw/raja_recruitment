<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ptk', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('status');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->string('attachment_path')->nullable()->after('created_by');

            // Upload mode doesn't require these fields -> make nullable
            $table->date('tanggal_permintaan')->nullable()->change();
            $table->string('department')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ptk', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['created_by', 'attachment_path']);
            $table->date('tanggal_permintaan')->nullable(false)->change();
            $table->string('department')->nullable(false)->change();
        });
    }
};

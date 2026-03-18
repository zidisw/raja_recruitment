<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize legacy roles to canonical 3-level role model.
        DB::table('users')->where('role', 'candidate')->update(['role' => 'user']);
        DB::table('users')->whereIn('role', ['hr', 'interviewer'])->update(['role' => 'admin']);
    }

    public function down(): void
    {
        // Best-effort rollback: map back to commonly used legacy values.
        DB::table('users')->where('role', 'user')->update(['role' => 'candidate']);
        DB::table('users')->where('role', 'admin')->update(['role' => 'hr']);
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('onboardings', function (Blueprint $table): void {
            if (! Schema::hasColumn('onboardings', 'travel_ticket_number')) {
                $table->string('travel_ticket_number')->nullable()->after('onboarding_status');
            }

            if (! Schema::hasColumn('onboardings', 'travel_ticket_notes')) {
                $table->text('travel_ticket_notes')->nullable()->after('travel_ticket_number');
            }

            if (! Schema::hasColumn('onboardings', 'travel_ticket_sent_at')) {
                $table->timestamp('travel_ticket_sent_at')->nullable()->after('travel_ticket_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('onboardings', function (Blueprint $table): void {
            if (Schema::hasColumn('onboardings', 'travel_ticket_sent_at')) {
                $table->dropColumn('travel_ticket_sent_at');
            }

            if (Schema::hasColumn('onboardings', 'travel_ticket_notes')) {
                $table->dropColumn('travel_ticket_notes');
            }

            if (Schema::hasColumn('onboardings', 'travel_ticket_number')) {
                $table->dropColumn('travel_ticket_number');
            }
        });
    }
};

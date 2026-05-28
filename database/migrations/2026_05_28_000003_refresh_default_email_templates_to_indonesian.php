<?php

declare(strict_types=1);

use App\Enums\RecruitmentStage;
use App\Models\EmailTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach (RecruitmentStage::emailTemplateStages() as $stage) {
            foreach (EmailTemplate::JOB_LEVELS as $jobLevel) {
                $defaults = EmailTemplate::defaultFor($stage);

                $existing = DB::table('email_templates')
                    ->where('stage', $stage->value)
                    ->where('job_level', $jobLevel)
                    ->first();

                if (! $existing) {
                    DB::table('email_templates')->insert([
                        'stage' => $stage->value,
                        'job_level' => $jobLevel,
                        'subject' => $defaults['subject'],
                        'body' => $defaults['body'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    continue;
                }

                if ($this->shouldRefresh($existing->subject, $existing->body)) {
                    DB::table('email_templates')
                        ->where('id', $existing->id)
                        ->update([
                            'subject' => $defaults['subject'],
                            'body' => $defaults['body'],
                            'updated_at' => $now,
                        ]);
                }
            }
        }
    }

    public function down(): void
    {
        //
    }

    private function shouldRefresh(?string $subject, ?string $body): bool
    {
        $subject = trim((string) $subject);
        $body = trim((string) $body);

        return $subject === ''
            || $body === ''
            || str_starts_with($subject, 'Application Update')
            || str_contains($subject, 'Application Received')
            || str_contains($body, 'Dear {name}')
            || str_contains($body, 'Your application for {job}')
            || str_contains($body, 'Best regards');
    }
};

<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\BulkCustomEmail;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessBulkCandidateEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $applicationIds,
        public string $subject,
        public string $body,
        public string $jobTitle
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Application::whereIn('id', $this->applicationIds)
            ->with('candidate')
            ->chunkById(100, function ($applications) {
                foreach ($applications as $app) {
                    try {
                        Mail::to($app->candidate->email)
                            ->send(new BulkCustomEmail(
                                subject: $this->subject,
                                body: $this->body,
                                candidateName: $app->candidate->name,
                                jobTitle: $this->jobTitle,
                            ));
                    } catch (\Throwable $e) {
                        // Continue processing remaining emails
                    }
                }
            });
    }
}

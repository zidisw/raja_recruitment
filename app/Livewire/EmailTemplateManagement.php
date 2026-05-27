<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Models\EmailTemplate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EmailTemplateManagement extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $editingStage = '';

    public string $editingStageLabel = '';

    public string $editingLevel = '';

    public string $subject = '';

    public string $body = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->canAccessRecruitment(), 403);
    }

    public function openEdit(string $stage, string $jobLevel): void
    {
        $recruitmentStage = RecruitmentStage::from($stage);

        $template = EmailTemplate::firstOrNew([
            'stage' => $stage,
            'job_level' => $jobLevel,
        ]);

        $this->editingId = $template->id ?? null;
        $this->editingStage = $stage;
        $this->editingStageLabel = $recruitmentStage->label();
        $this->editingLevel = $jobLevel;
        $this->subject = $template->subject ?? "Application Update: {$recruitmentStage->label()}";
        $this->body = $template->body ?? "Dear {name},\n\nYour application for {job} has been updated to: {status}.\n\nBest regards,\nRecruitment Team";
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $template = EmailTemplate::firstOrNew([
            'stage' => $this->editingStage,
            'job_level' => $this->editingLevel,
        ]);

        $template->subject = $this->subject;
        $template->body = $this->body;
        $template->save();

        $this->showModal = false;
        $this->reset(['editingId', 'editingStage', 'editingStageLabel', 'editingLevel', 'subject', 'body']);

        $this->dispatch('notify', ['message' => __('Email template saved successfully.'), 'type' => 'success']);
    }

    public function render(): \Illuminate\View\View
    {
        $stages = RecruitmentStage::emailTemplateStages();

        $templates = EmailTemplate::all()->keyBy(fn ($t) => $t->stage->value.'_'.$t->job_level);

        return view('livewire.email-template-management', [
            'stages' => array_values($stages),
            'templates' => $templates,
        ]);
    }
}

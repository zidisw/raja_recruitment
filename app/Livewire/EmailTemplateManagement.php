<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EmailTemplateManagement extends Component
{
    public bool $showModal = false;

    public $editingId = null;

    public string $editingStage = '';

    public string $editingStageLabel = '';

    public string $editingLevel = '';

    public string $subject = '';

    public string $body = '';

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user instanceof User && $user->canAccessRecruitment(), 403);
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
        $defaults = EmailTemplate::defaultFor($recruitmentStage);
        $this->subject = $template->subject ?? $defaults['subject'];
        $this->body = $template->body ?? $defaults['body'];
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

        $this->dispatch('notify', ['message' => 'Template email berhasil disimpan.', 'type' => 'success']);
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

<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Psychotest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PsychotestManagement extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $application_id = null;
    public string $test_date = '';
    public string $result = 'passed';
    public string $notes = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->canAccessRecruitment(), 403);
    }

    public function openCreate(): void
    {
        $this->reset(['application_id', 'test_date', 'notes']);
        $this->result = 'passed';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'application_id' => ['required', 'exists:applications,id'],
            'test_date' => ['required', 'date'],
            'result' => ['required', 'in:passed,failed'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $psychotest = Psychotest::updateOrCreate(
            ['application_id' => $validated['application_id']],
            $validated
        );

        $application = $psychotest->application;
        if ($validated['result'] === 'passed') {
            $application->update([
                'recruitment_stage' => RecruitmentStage::MCU,
                'stage_updated_at' => now(),
            ]);
        } else {
            $application->update([
                'recruitment_stage' => RecruitmentStage::REJECTED,
                'stage_updated_at' => now(),
            ]);
        }

        $this->showModal = false;
        $this->dispatch('notify', message: __('Psychotest result saved.'), type: 'success');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.psychotest-management', [
            'psychotests' => Psychotest::with('application.candidate', 'application.job')
                ->latest('test_date')
                ->paginate(10),
            'applications' => Application::with(['candidate', 'job'])
                ->whereIn('recruitment_stage', [RecruitmentStage::PSYCHOTEST, RecruitmentStage::MCU])
                ->orWhereHas('psychotest')
                ->get(),
        ]);
    }
}

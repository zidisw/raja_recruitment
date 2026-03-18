<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Mcu;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class McuManagement extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $application_id = null;
    public string $mcu_date = '';
    public string $result = 'fit';
    public string $notes = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->canAccessRecruitment(), 403);
    }

    public function openCreate(): void
    {
        $this->reset(['application_id', 'mcu_date', 'notes']);
        $this->result = 'fit';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'application_id' => ['required', 'exists:applications,id'],
            'mcu_date' => ['required', 'date'],
            'result' => ['required', 'in:fit,unfit'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $mcu = Mcu::updateOrCreate(['application_id' => $validated['application_id']], $validated);

        $application = $mcu->application;
        if ($validated['result'] === 'fit') {
            $application->update([
                'recruitment_stage' => RecruitmentStage::ONBOARDING,
                'stage_updated_at' => now(),
            ]);
        } else {
            $application->update([
                'recruitment_stage' => RecruitmentStage::REJECTED,
                'stage_updated_at' => now(),
            ]);
        }

        $this->showModal = false;
        $this->dispatch('notify', message: __('MCU result saved.'), type: 'success');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.mcu-management', [
            'mcus' => Mcu::with('application.candidate', 'application.job')->latest('mcu_date')->paginate(10),
            'applications' => Application::with(['candidate', 'job'])
                ->whereIn('recruitment_stage', [RecruitmentStage::MCU, RecruitmentStage::ONBOARDING])
                ->orWhereHas('mcu')
                ->get(),
        ]);
    }
}

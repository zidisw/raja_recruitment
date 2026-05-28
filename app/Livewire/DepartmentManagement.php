<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DepartmentManagement extends Component
{
    public bool $showModal = false;

    public $editingId = null;

    public string $name = '';

    public string $description = '';

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user instanceof User && $user->isSuperAdmin(), 403);
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'description']);
        $this->showModal = true;
    }

    public function openEdit(int $departmentId): void
    {
        $department = Department::findOrFail($departmentId);

        $this->editingId = $department->id;
        $this->name = $department->name;
        $this->description = $department->description ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        if ($this->editingId) {
            Department::findOrFail($this->editingId)->update($validated);
            $this->dispatch('notify', ['message' => 'Department updated successfully.', 'type' => 'success']);
        } else {
            Department::create($validated);
            $this->dispatch('notify', ['message' => 'Department created successfully.', 'type' => 'success']);
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'description']);
    }

    public function delete(int $departmentId): void
    {
        $department = Department::findOrFail($departmentId);

        $department->delete();
        $this->dispatch('notify', ['message' => 'Department deleted successfully.', 'type' => 'success']);
    }

    public function render()
    {
        return view('livewire.department-management', [
            'departments' => Department::withCount(['users', 'jobs'])->orderBy('name')->get(),
        ]);
    }
}

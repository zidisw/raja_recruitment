<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SiteManagement extends Component
{
    public bool $showModal = false;

    public $editingId = null;

    public string $name = '';

    public string $location = '';

    public string $description = '';

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user instanceof User && $user->isSuperAdmin(), 403);
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'location', 'description']);
        $this->showModal = true;
    }

    public function openEdit(int $siteId): void
    {
        $site = Site::findOrFail($siteId);

        $this->editingId = $site->id;
        $this->name = $site->name;
        $this->location = $site->location ?? '';
        $this->description = $site->description ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        if ($this->editingId) {
            Site::findOrFail($this->editingId)->update($validated);
            $this->dispatch('notify', ['message' => 'Site updated successfully.', 'type' => 'success']);
        } else {
            Site::create($validated);
            $this->dispatch('notify', ['message' => 'Site created successfully.', 'type' => 'success']);
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'location', 'description']);
    }

    public function delete(int $siteId): void
    {
        $site = Site::findOrFail($siteId);

        $site->delete();
        $this->dispatch('notify', ['message' => 'Site deleted successfully.', 'type' => 'success']);
    }

    public function render()
    {
        return view('livewire.site-management', [
            'sites' => Site::withCount('jobs')->orderBy('name')->get(),
        ]);
    }
}

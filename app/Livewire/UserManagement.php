<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UserManagement extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public $editingId = null;

    public string $name = '';

    public string $email = '';

    public string $role = '';

    public $department_id = null;

    public string $password = '';

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user instanceof User && $user->isSuperAdmin(), 403);
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'email', 'role', 'department_id', 'password']);
        $this->showModal = true;
    }

    public function openEdit(int $userId): void
    {
        $user = User::findOrFail($userId);

        $this->reset(['password']);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->normalized()->value;
        $this->department_id = $user->department_id;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->editingId],
            'role' => ['required', 'string', 'in:'.implode(',', array_column(UserRole::assignableCases(), 'value'))],
            'department_id' => ['nullable', 'exists:departments,id'],
        ];

        if (! $this->editingId) {
            $rules['password'] = ['required', 'string', 'min:8'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8'];
        }

        $validated = $this->validate($rules);

        // Only admin can optionally be bound to a department in this setup.
        $role = UserRole::from($validated['role']);
        if ($role !== UserRole::Admin) {
            $validated['department_id'] = null;
        }

        if ($this->editingId) {
            $updateData = collect($validated)->except('password')->toArray();
            if (! empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }
            User::findOrFail($this->editingId)->update($updateData);
            $this->dispatch('notify', ['message' => 'User updated successfully.', 'type' => 'success']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
            $validated['email_verified_at'] = now();
            User::create($validated);
            $this->dispatch('notify', ['message' => 'User created successfully.', 'type' => 'success']);
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'email', 'role', 'department_id', 'password']);
    }

    public function render()
    {
        $users = User::with('department')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.user-management', [
            'users' => $users,
            'departments' => Department::orderBy('name')->get(),
            'roles' => UserRole::assignableCases(),
        ]);
    }
}

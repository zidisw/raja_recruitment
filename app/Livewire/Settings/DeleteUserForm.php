<?php

namespace App\Livewire\Settings;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class DeleteUserForm extends Component
{
    use PasswordValidationRules;

    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        $userId = Auth::id();

        if (! $userId) {
            return;
        }

        User::query()->whereKey($userId)->delete();

        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();

        $this->redirect('/', navigate: true);
    }
}

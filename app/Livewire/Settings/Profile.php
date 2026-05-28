<?php

namespace App\Livewire\Settings;

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Profile settings')]
class Profile extends Component
{
    use ProfileValidationRules;

    public string $name = '';

    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = $this->currentUser();

        $this->name = $user->name;
        $this->email = $user->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = $this->currentUser();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', ['name' => $user->name]);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = $this->currentUser();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        $this->dispatch('notify', ['message' => __('Verification link sent successfully.'), 'type' => 'success']);
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        $user = $this->currentUser();

        return $user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        $user = $this->currentUser();

        return ! $user instanceof MustVerifyEmail
            || ($user instanceof MustVerifyEmail && $user->hasVerifiedEmail());
    }

    private function currentUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return $user;
    }
}

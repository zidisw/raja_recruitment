<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

#[Layout('components.layouts.guest')]
class Register extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|email|max:255|unique:users')]
    public string $email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    public function register(): Redirector|RedirectResponse
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => UserRole::User,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.register');
    }
}
<?php

declare(strict_types=1);

namespace App\Livewire\Candidate;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\UserRole;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\CandidateOrganization;
use App\Models\CandidateProfile;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class MyProfile extends Component
{
    use PasswordValidationRules;
    use ProfileValidationRules;
    use WithFileUploads;

    public string $activeTab = 'account';

    // Account tab
    public string $name = '';

    public string $email = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    // Personal data tab
    public string $nik = '';

    public string $place_of_birth = '';

    public string $date_of_birth = '';

    public string $gender = '';

    public string $religion = '';

    public string $marital_status = '';

    public string $address_ktp = '';

    public string $address_domicile = '';

    public string $whatsapp = '';

    public string $linkedin_url = '';

    /** @var \Illuminate\Http\UploadedFile|null */
    public $photo = null;

    /** @var \Illuminate\Http\UploadedFile|null */
    public $ktp_file = null;

    /** @var \Illuminate\Http\UploadedFile|null */
    public $portfolio = null;

    /** @var \Illuminate\Http\UploadedFile|null */
    public $certificate = null;

    /** @var \Illuminate\Http\UploadedFile|null */
    public $paklaring = null;

    // Education
    public array $educations = [];

    // Work experience
    public array $experiences = [];

    // Organizations
    public array $organizations = [];

    public function mount(): void
    {
        abort_unless(Auth::user()?->hasUserRole(), 403);

        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;

        $profile = $user->profile;
        if ($profile) {
            $this->nik = $profile->nik ?? '';
            $this->place_of_birth = $profile->place_of_birth ?? '';
            $this->date_of_birth = $profile->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : '';
            $this->gender = $profile->gender ?? '';
            $this->religion = $profile->religion ?? '';
            $this->marital_status = $profile->marital_status ?? '';
            $this->address_ktp = $profile->address_ktp ?? '';
            $this->address_domicile = $profile->address_domicile ?? '';
            $this->whatsapp = $profile->whatsapp ?? '';
            $this->linkedin_url = $profile->linkedin_url ?? '';
        }

        $this->educations = $user->education->map(fn ($edu) => [
            'id' => $edu->id,
            'degree' => $edu->degree,
            'institution_name' => $edu->institution_name,
            'major' => $edu->major,
            'start_year' => (string) $edu->start_year,
            'end_year' => (string) ($edu->end_year ?? ''),
            'gpa' => (string) ($edu->gpa ?? ''),
        ])->toArray();

        if (empty($this->educations)) {
            $this->addEducation();
        }

        $this->experiences = $user->experiences->map(fn ($exp) => [
            'id' => $exp->id,
            'company_name' => $exp->company_name,
            'position' => $exp->position,
            'start_date' => $exp->start_date ? $exp->start_date->format('Y-m-d') : '',
            'end_date' => $exp->end_date ? $exp->end_date->format('Y-m-d') : '',
            'is_current' => (bool) $exp->is_current,
            'last_salary' => (string) ($exp->last_salary ?? ''),
            'job_description' => $exp->job_description ?? '',
        ])->toArray();

        $this->organizations = $user->organizations->map(fn ($org) => [
            'id' => $org->id,
            'organization_name' => $org->organization_name,
            'position' => $org->position,
            'start_date' => $org->start_date ? $org->start_date->format('Y-m-d') : '',
            'end_date' => $org->end_date ? $org->end_date->format('Y-m-d') : '',
            'is_current' => (bool) $org->is_current,
        ])->toArray();
    }

    public function updateAccount(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', ['name' => $user->name]);
        $this->dispatch('notify', ['message' => 'Account information updated successfully.', 'type' => 'success']);
    }

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('notify', ['message' => 'Password updated successfully.', 'type' => 'success']);
    }

    public function savePersonalInfo(): void
    {
        $this->validate([
            'nik' => ['required', 'string', 'digits:16'],
            'place_of_birth' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female'],
            'religion' => ['required', 'string', 'max:50'],
            'marital_status' => ['required', 'in:single,married,divorced,widowed'],
            'address_ktp' => ['required', 'string', 'max:500'],
            'address_domicile' => ['required', 'string', 'max:500'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
        ]);

        $profileData = [
            'nik' => $this->nik,
            'place_of_birth' => $this->place_of_birth,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'religion' => $this->religion,
            'marital_status' => $this->marital_status,
            'address_ktp' => $this->address_ktp,
            'address_domicile' => $this->address_domicile,
            'whatsapp' => $this->whatsapp,
            'linkedin_url' => $this->linkedin_url ?: null,
        ];

        CandidateProfile::updateOrCreate(['user_id' => Auth::id()], $profileData);
        $this->dispatch('notify', ['message' => 'Personal information updated successfully.', 'type' => 'success']);
    }

    public function saveDocuments(): void
    {
        $this->validate([
            'photo' => ['nullable', 'image', 'max:2048'],
            'ktp_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'portfolio' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'paklaring' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $profileData = [];

        if ($this->photo) {
            $profileData['photo_path'] = $this->photo->store('candidate/photos', 'public');
            $this->photo = null;
        }
        if ($this->ktp_file) {
            $profileData['ktp_path'] = $this->ktp_file->store('candidate/ktp', 'public');
            $this->ktp_file = null;
        }
        if ($this->portfolio) {
            $profileData['portfolio_path'] = $this->portfolio->store('candidate/portfolios', 'public');
            $this->portfolio = null;
        }
        if ($this->certificate) {
            $profileData['certificate_path'] = $this->certificate->store('candidate/certificates', 'public');
            $this->certificate = null;
        }
        if ($this->paklaring) {
            $profileData['paklaring_path'] = $this->paklaring->store('candidate/paklaring', 'public');
            $this->paklaring = null;
        }

        if (!empty($profileData)) {
            CandidateProfile::updateOrCreate(['user_id' => Auth::id()], $profileData);
            $this->dispatch('notify', ['message' => 'Documents updated successfully.', 'type' => 'success']);
        } else {
            $this->dispatch('notify', ['message' => 'No new documents to upload.', 'type' => 'info']);
        }
    }

    public function saveEducation(): void
    {
        $this->validate([
            'educations' => ['required', 'array', 'min:1'],
            'educations.*.degree' => ['required', 'string', 'max:50'],
            'educations.*.institution_name' => ['required', 'string', 'max:255'],
            'educations.*.major' => ['required', 'string', 'max:255'],
            'educations.*.start_year' => ['required', 'integer', 'min:1970', 'max:' . date('Y')],
            'educations.*.end_year' => ['nullable', 'integer', 'min:1970', 'max:' . (date('Y') + 6)],
            'educations.*.gpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
        ]);

        $user = Auth::user();
        $user->education()->delete();
        foreach ($this->educations as $edu) {
            CandidateEducation::create([
                'user_id' => $user->id,
                'degree' => $edu['degree'],
                'institution_name' => $edu['institution_name'],
                'major' => $edu['major'],
                'start_year' => $edu['start_year'],
                'end_year' => $edu['end_year'] ?: null,
                'gpa' => $edu['gpa'] ?: null,
            ]);
        }

        $this->dispatch('notify', ['message' => 'Education history updated successfully.', 'type' => 'success']);
    }

    public function saveExperience(): void
    {
        $this->validate([
            'experiences' => ['nullable', 'array'],
            'experiences.*.company_name' => ['required', 'string', 'max:255'],
            'experiences.*.position' => ['required', 'string', 'max:255'],
            'experiences.*.start_date' => ['required', 'date'],
            'experiences.*.end_date' => ['nullable', 'date'],
            'experiences.*.is_current' => ['boolean'],
            'experiences.*.last_salary' => ['nullable', 'numeric', 'min:0'],
            'experiences.*.job_description' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = Auth::user();
        $user->experiences()->delete();
        foreach ($this->experiences as $exp) {
            CandidateExperience::create([
                'user_id' => $user->id,
                'company_name' => $exp['company_name'],
                'position' => $exp['position'],
                'start_date' => $exp['start_date'],
                'end_date' => ($exp['is_current'] ?? false) ? null : ($exp['end_date'] ?: null),
                'is_current' => (bool) ($exp['is_current'] ?? false),
                'last_salary' => $exp['last_salary'] ?: null,
                'job_description' => $exp['job_description'] ?: null,
            ]);
        }

        $this->dispatch('notify', ['message' => 'Work experience updated successfully.', 'type' => 'success']);
    }

    public function saveOrganization(): void
    {
        $this->validate([
            'organizations' => ['nullable', 'array'],
            'organizations.*.organization_name' => ['required', 'string', 'max:255'],
            'organizations.*.position' => ['required', 'string', 'max:255'],
            'organizations.*.start_date' => ['required', 'date'],
            'organizations.*.end_date' => ['nullable', 'date'],
            'organizations.*.is_current' => ['boolean'],
        ]);

        $user = Auth::user();
        $user->organizations()->delete();
        foreach ($this->organizations as $org) {
            CandidateOrganization::create([
                'user_id' => $user->id,
                'organization_name' => $org['organization_name'],
                'position' => $org['position'],
                'start_date' => $org['start_date'],
                'end_date' => ($org['is_current'] ?? false) ? null : ($org['end_date'] ?: null),
                'is_current' => (bool) ($org['is_current'] ?? false),
            ]);
        }

        $this->dispatch('notify', ['message' => 'Organizational experience updated successfully.', 'type' => 'success']);
    }

    public function addEducation(): void
    {
        $this->educations[] = [
            'degree' => '',
            'institution_name' => '',
            'major' => '',
            'start_year' => '',
            'end_year' => '',
            'gpa' => '',
        ];
    }

    public function removeEducation(int $index): void
    {
        array_splice($this->educations, $index, 1);
    }

    public function addExperience(): void
    {
        $this->experiences[] = [
            'company_name' => '',
            'position' => '',
            'start_date' => '',
            'end_date' => '',
            'is_current' => false,
            'last_salary' => '',
            'job_description' => '',
        ];
    }

    public function removeExperience(int $index): void
    {
        array_splice($this->experiences, $index, 1);
    }

    public function addOrganization(): void
    {
        $this->organizations[] = [
            'organization_name' => '',
            'position' => '',
            'start_date' => '',
            'end_date' => '',
            'is_current' => false,
        ];
    }

    public function removeOrganization(int $index): void
    {
        array_splice($this->organizations, $index, 1);
    }

    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.candidate.my-profile', [
            'profile' => Auth::user()->profile,
        ]);
    }
}

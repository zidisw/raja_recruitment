<?php

declare(strict_types=1);

namespace App\Livewire\Candidate;

use App\Enums\UserRole;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\CandidateOrganization;
use App\Models\CandidateProfile;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.candidate')]
class ProfileSetup extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public int $totalSteps = 5;

    // Step 1: Personal Data
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

    public $photo = null;

    public $ktp_file = null;

    // Step 2: Education (array of entries)
    public array $educations = [];

    // Step 3: Work Experience (array of entries)
    public array $experiences = [];

    // Step 4: Organizations (array of entries)
    public array $organizations = [];

    // Step 5: Documents
    public $portfolio = null;

    public $certificate = null;

    public $paklaring = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasUserRole(), 403);

        // If profile already exists, redirect to portal
        if (auth()->user()->profile) {
            $this->redirect(route('candidate.portal'));

            return;
        }

        // Initialize with one empty education entry
        $this->addEducation();
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();
        $this->step++;
    }

    public function previousStep(): void
    {
        $this->step = max(1, $this->step - 1);
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

    public function save(): void
    {
        // Re-validate all steps to prevent tampered Livewire state from bypassing step guards
        $this->validate([
            // Step 1 – Personal data
            'nik'              => ['required', 'string', 'digits:16'],
            'place_of_birth'   => ['required', 'string', 'max:100'],
            'date_of_birth'    => ['required', 'date', 'before:today'],
            'gender'           => ['required', 'in:male,female'],
            'religion'         => ['required', 'string', 'max:50'],
            'marital_status'   => ['required', 'in:single,married,divorced,widowed'],
            'address_ktp'      => ['required', 'string', 'max:500'],
            'address_domicile' => ['required', 'string', 'max:500'],
            'whatsapp'         => ['required', 'string', 'max:20'],
            'linkedin_url'     => ['nullable', 'url', 'max:255'],
            'photo'            => ['required', 'image', 'max:2048'],
            'ktp_file'         => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            // Step 2 – Education
            'educations'                     => ['required', 'array', 'min:1'],
            'educations.*.degree'            => ['required', 'string', 'max:50'],
            'educations.*.institution_name'  => ['required', 'string', 'max:255'],
            'educations.*.major'             => ['required', 'string', 'max:255'],
            'educations.*.start_year'        => ['required', 'integer', 'min:1970', 'max:' . date('Y')],
            'educations.*.end_year'          => ['nullable', 'integer', 'min:1970', 'max:' . (date('Y') + 6)],
            'educations.*.gpa'              => ['nullable', 'numeric', 'min:0', 'max:4'],
            // Step 3 – Work experience
            'experiences'                       => ['nullable', 'array'],
            'experiences.*.company_name'        => ['required', 'string', 'max:255'],
            'experiences.*.position'            => ['required', 'string', 'max:255'],
            'experiences.*.start_date'          => ['required', 'date'],
            'experiences.*.end_date'            => ['nullable', 'date'],
            'experiences.*.is_current'          => ['boolean'],
            'experiences.*.last_salary'         => ['nullable', 'numeric', 'min:0'],
            'experiences.*.job_description'     => ['nullable', 'string', 'max:2000'],
            // Step 4 – Organizations
            'organizations'                          => ['nullable', 'array'],
            'organizations.*.organization_name'      => ['required', 'string', 'max:255'],
            'organizations.*.position'               => ['required', 'string', 'max:255'],
            'organizations.*.start_date'             => ['required', 'date'],
            'organizations.*.end_date'               => ['nullable', 'date'],
            'organizations.*.is_current'             => ['boolean'],
            // Step 5 – Documents
            'portfolio'   => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'paklaring'   => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $user = auth()->user();

        $photoPath = $this->photo?->store('candidate/photos', 'public');
        $ktpPath = $this->ktp_file?->store('candidate/ktp', 'public');
        $portfolioPath = $this->portfolio?->store('candidate/portfolios', 'public');
        $certificatePath = $this->certificate?->store('candidate/certificates', 'public');
        $paklaringPath = $this->paklaring?->store('candidate/paklaring', 'public');

        CandidateProfile::create([
            'user_id' => $user->id,
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
            'photo_path' => $photoPath,
            'ktp_path' => $ktpPath,
            'portfolio_path' => $portfolioPath,
            'certificate_path' => $certificatePath,
            'paklaring_path' => $paklaringPath,
        ]);

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

        session()->flash('success', 'Profile setup completed successfully!');
        $this->redirect(route('candidate.portal'));
    }

    public function render()
    {
        return view('livewire.candidate.profile-setup');
    }

    private function validateCurrentStep(): void
    {
        match ($this->step) {
            1 => $this->validate([
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
                'photo' => ['required', 'image', 'max:2048'],
                'ktp_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            ]),
            2 => $this->validate([
                'educations' => ['required', 'array', 'min:1'],
                'educations.*.degree' => ['required', 'string', 'max:50'],
                'educations.*.institution_name' => ['required', 'string', 'max:255'],
                'educations.*.major' => ['required', 'string', 'max:255'],
                'educations.*.start_year' => ['required', 'integer', 'min:1970', 'max:' . date('Y')],
                'educations.*.end_year' => ['nullable', 'integer', 'min:1970', 'max:' . (date('Y') + 6)],
                'educations.*.gpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            ]),
            3 => $this->validate([
                'experiences' => ['nullable', 'array'],
                'experiences.*.company_name' => ['required', 'string', 'max:255'],
                'experiences.*.position' => ['required', 'string', 'max:255'],
                'experiences.*.start_date' => ['required', 'date'],
                'experiences.*.end_date' => ['nullable', 'date'],
                'experiences.*.is_current' => ['boolean'],
                'experiences.*.last_salary' => ['nullable', 'numeric', 'min:0'],
                'experiences.*.job_description' => ['nullable', 'string', 'max:2000'],
            ]),
            4 => $this->validate([
                'organizations' => ['nullable', 'array'],
                'organizations.*.organization_name' => ['required', 'string', 'max:255'],
                'organizations.*.position' => ['required', 'string', 'max:255'],
                'organizations.*.start_date' => ['required', 'date'],
                'organizations.*.end_date' => ['nullable', 'date'],
                'organizations.*.is_current' => ['boolean'],
            ]),
            default => null,
        };
    }
}

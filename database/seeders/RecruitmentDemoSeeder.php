<?php

namespace Database\Seeders;

use App\Enums\RecruitmentStage;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\ApplicationStageLog;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\CandidateOrganization;
use App\Models\CandidateProfile;
use App\Models\Department;
use App\Models\Interview;
use App\Models\Job;
use App\Models\Mcu;
use App\Models\OfferingLetter;
use App\Models\Onboarding;
use App\Models\Psychotest;
use App\Models\Ptk;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RecruitmentDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->putDemoFiles();

            $department = Department::firstOrCreate(
                ['name' => 'Operations Demo'],
                ['description' => 'Demo department for recruitment end-to-end flow.']
            );

            $site = Site::firstOrCreate(
                ['name' => 'Site Demo Morowali'],
                [
                    'location' => 'Morowali, Sulawesi Tengah',
                    'description' => 'Demo site for recruitment workflow testing.',
                ]
            );

            $superadmin = $this->user('Demo Super Admin', 'demo.superadmin@rjs.test', UserRole::SuperAdmin);
            $admin = $this->user('Demo HR Admin', 'demo.hr@rjs.test', UserRole::Admin, $department->id);
            $interviewer = $this->user('Demo Interviewer', 'demo.interviewer@rjs.test', UserRole::Admin, $department->id);

            $ptk = Ptk::updateOrCreate(
                ['nomor_ptk' => 'DEMO-PTK-E2E-001'],
                [
                    'department' => $department->name,
                    'posisi' => 'Demo Heavy Equipment Operator',
                    'jumlah_kebutuhan' => 3,
                    'alasan_permintaan' => 'Demo kebutuhan tenaga kerja untuk simulasi ATS end-to-end.',
                    'tanggal_permintaan' => now()->subDays(10)->toDateString(),
                    'status' => 'approved',
                    'created_by' => $superadmin->id,
                    'attachment_path' => 'demo/recruitment/ptk-demo.pdf',
                ]
            );

            $job = Job::updateOrCreate(
                ['ptk_id' => $ptk->id],
                [
                    'title' => 'Demo Heavy Equipment Operator',
                    'description' => 'Mengoperasikan unit alat berat sesuai SOP operasional tambang.',
                    'requirements' => "SIM B2 aktif\nPengalaman operator minimal 2 tahun\nSiap ditempatkan di site",
                    'benefits' => "Mess\nTransport site\nAsuransi kesehatan",
                    'level' => 'staff',
                    'is_active' => true,
                    'closed_at' => now()->addMonths(2)->toDateString(),
                    'created_by' => $superadmin->id,
                    'department_id' => $department->id,
                    'site_id' => $site->id,
                ]
            );

            $pipeline = [
                [RecruitmentStage::APPLIED, 'Demo Applied Candidate'],
                [RecruitmentStage::HR_INTERVIEW, 'Demo HR Interview Candidate'],
                [RecruitmentStage::USER_INTERVIEW, 'Demo User Interview Candidate'],
                [RecruitmentStage::OFFERING, 'Demo Offering Candidate'],
                [RecruitmentStage::PSYCHOTEST, 'Demo Psychotest Candidate'],
                [RecruitmentStage::MCU, 'Demo MCU Candidate'],
                [RecruitmentStage::ONBOARDING, 'Demo Onboarding Candidate'],
                [RecruitmentStage::HIRED, 'Demo Hired Candidate'],
                [RecruitmentStage::REJECTED, 'Demo Rejected Candidate'],
            ];

            foreach ($pipeline as [$stage, $name]) {
                $candidate = $this->candidate(
                    $name,
                    'demo.'.strtolower($stage->value).'@rjs.test',
                    $stage->toInt()
                );

                $application = Application::updateOrCreate(
                    [
                        'user_id' => $candidate->id,
                        'job_id' => $job->id,
                    ],
                    [
                        'recruitment_stage' => $stage,
                        'stage_updated_at' => now()->subDays(max(0, 9 - $stage->toInt())),
                        'hr_notes' => 'Demo application seeded at '.$stage->label().' stage.',
                    ]
                );

                $this->syncArtifactsForStage($application, $stage, $admin, $interviewer);
            }
        });
    }

    private function user(string $name, string $email, UserRole $role, ?int $departmentId = null): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => $role,
                'department_id' => $departmentId,
            ]
        );
    }

    private function candidate(string $name, string $email, int $seedNumber): User
    {
        $user = $this->user($name, $email, UserRole::User);

        CandidateProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nik' => str_pad((string) (7300000000000000 + $seedNumber), 16, '0', STR_PAD_LEFT),
                'place_of_birth' => 'Makassar',
                'date_of_birth' => now()->subYears(28)->subDays($seedNumber)->toDateString(),
                'gender' => $seedNumber % 2 === 0 ? 'male' : 'female',
                'religion' => 'Islam',
                'marital_status' => 'single',
                'address_ktp' => 'Jl. Demo KTP No. '.$seedNumber.', Makassar',
                'address_domicile' => 'Jl. Demo Domisili No. '.$seedNumber.', Makassar',
                'whatsapp' => '08123456'.str_pad((string) $seedNumber, 4, '0', STR_PAD_LEFT),
                'linkedin_url' => 'https://linkedin.com/in/demo-candidate-'.$seedNumber,
                'photo_path' => 'demo/recruitment/photo-demo.jpg',
                'ktp_path' => 'demo/recruitment/ktp-demo.pdf',
                'portfolio_path' => 'demo/recruitment/portfolio-demo.pdf',
                'certificate_path' => 'demo/recruitment/certificate-demo.pdf',
                'paklaring_path' => 'demo/recruitment/paklaring-demo.pdf',
            ]
        );

        CandidateEducation::updateOrCreate(
            ['user_id' => $user->id, 'institution_name' => 'Politeknik Demo RJS'],
            [
                'degree' => 'D3',
                'major' => 'Teknik Mesin',
                'start_year' => 2016,
                'end_year' => 2019,
                'gpa' => 3.45,
            ]
        );

        CandidateExperience::updateOrCreate(
            ['user_id' => $user->id, 'company_name' => 'PT Demo Mining Services'],
            [
                'position' => 'Heavy Equipment Operator',
                'start_date' => now()->subYears(4)->toDateString(),
                'end_date' => now()->subMonths(2)->toDateString(),
                'is_current' => false,
                'last_salary' => 6500000,
                'job_description' => 'Mengoperasikan excavator dan melakukan pre-start check harian.',
            ]
        );

        CandidateOrganization::updateOrCreate(
            ['user_id' => $user->id, 'organization_name' => 'Komunitas Operator Demo'],
            [
                'position' => 'Member',
                'start_date' => now()->subYears(3)->toDateString(),
                'end_date' => null,
                'is_current' => true,
            ]
        );

        return $user;
    }

    private function syncArtifactsForStage(Application $application, RecruitmentStage $stage, User $admin, User $interviewer): void
    {
        ApplicationStageLog::where('application_id', $application->id)
            ->where('notes', 'like', 'Demo:%')
            ->delete();

        $stages = RecruitmentStage::pipelineStages();
        $targetIndex = array_search($stage, $stages, true);
        $targetIndex = $targetIndex === false ? 0 : $targetIndex;

        foreach (array_slice($stages, 0, $targetIndex + 1) as $passedStage) {
            ApplicationStageLog::create([
                'application_id' => $application->id,
                'stage' => $passedStage,
                'decision' => 'passed',
                'notes' => 'Demo: kandidat mencapai tahap '.$passedStage->label(),
                'decided_by' => $admin->id,
            ]);
        }

        if ($stage === RecruitmentStage::REJECTED) {
            ApplicationStageLog::create([
                'application_id' => $application->id,
                'stage' => RecruitmentStage::HR_INTERVIEW,
                'decision' => 'rejected',
                'notes' => 'Demo: kandidat ditolak saat simulasi interview.',
                'decided_by' => $admin->id,
            ]);
        }

        if ($targetIndex >= array_search(RecruitmentStage::HR_INTERVIEW, $stages, true)) {
            Interview::updateOrCreate(
                [
                    'application_id' => $application->id,
                    'interview_type' => 'HR Interview',
                ],
                [
                    'interviewer_id' => $interviewer->id,
                    'scheduled_at' => now()->subDays(6)->setTime(9, 0),
                    'meeting_link' => 'https://meet.example.test/hr-'.$application->id,
                    'evaluation_path' => 'demo/recruitment/hr-evaluation-demo.pdf',
                    'hr_notes' => 'Demo HR interview evaluation.',
                    'status' => $stage === RecruitmentStage::REJECTED ? 'failed' : 'passed',
                ]
            );
        }

        if ($targetIndex >= array_search(RecruitmentStage::USER_INTERVIEW, $stages, true)) {
            Interview::updateOrCreate(
                [
                    'application_id' => $application->id,
                    'interview_type' => 'User Interview',
                ],
                [
                    'interviewer_id' => $interviewer->id,
                    'scheduled_at' => now()->subDays(5)->setTime(13, 30),
                    'meeting_link' => 'https://meet.example.test/user-'.$application->id,
                    'evaluation_path' => 'demo/recruitment/user-evaluation-demo.pdf',
                    'hr_notes' => 'Demo user interview evaluation.',
                    'status' => 'passed',
                ]
            );
        }

        if ($targetIndex >= array_search(RecruitmentStage::OFFERING, $stages, true)) {
            OfferingLetter::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'offer_date' => now()->subDays(4)->toDateString(),
                    'file_path' => 'demo/recruitment/offering-demo.pdf',
                    'signed_file_path' => $targetIndex >= array_search(RecruitmentStage::PSYCHOTEST, $stages, true)
                        ? 'demo/recruitment/offering-signed-demo.pdf'
                        : null,
                    'signed_at' => $targetIndex >= array_search(RecruitmentStage::PSYCHOTEST, $stages, true) ? now()->subDays(3) : null,
                    'status' => $targetIndex >= array_search(RecruitmentStage::PSYCHOTEST, $stages, true)
                        ? 'accepted'
                        : 'waiting_response',
                    'candidate_notes' => 'Demo offering letter.',
                ]
            );
        }

        if ($targetIndex >= array_search(RecruitmentStage::PSYCHOTEST, $stages, true)) {
            Psychotest::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'test_date' => now()->subDays(3)->toDateString(),
                    'result' => 'passed',
                    'notes' => 'Demo psychotest passed.',
                    'file_path' => 'demo/recruitment/psychotest-demo.pdf',
                ]
            );
        }

        if ($targetIndex >= array_search(RecruitmentStage::MCU, $stages, true)) {
            Mcu::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'mcu_date' => now()->subDays(2)->toDateString(),
                    'result' => 'fit',
                    'notes' => 'Demo MCU fit to work.',
                    'file_path' => 'demo/recruitment/mcu-demo.pdf',
                ]
            );
        }

        if ($targetIndex >= array_search(RecruitmentStage::ONBOARDING, $stages, true)) {
            Onboarding::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'joining_date' => now()->addDays(7)->toDateString(),
                    'onboarding_status' => $stage === RecruitmentStage::HIRED ? 'completed' : 'pending',
                    'travel_ticket_number' => 'RJS-DEMO-'.$application->id,
                    'travel_ticket_notes' => 'Demo ticket issued for site arrival.',
                    'travel_ticket_sent_at' => now()->subDay(),
                    'onsite_date' => now()->addDays(6)->toDateString(),
                    'onsite_location' => 'Site Demo Morowali',
                    'onsite_notes' => 'Demo onboarding onsite schedule.',
                ]
            );
        }
    }

    private function putDemoFiles(): void
    {
        $files = [
            'ptk-demo.pdf',
            'ktp-demo.pdf',
            'portfolio-demo.pdf',
            'certificate-demo.pdf',
            'paklaring-demo.pdf',
            'hr-evaluation-demo.pdf',
            'user-evaluation-demo.pdf',
            'offering-demo.pdf',
            'offering-signed-demo.pdf',
            'psychotest-demo.pdf',
            'mcu-demo.pdf',
        ];

        foreach ($files as $file) {
            Storage::disk('public')->put('demo/recruitment/'.$file, "Demo file for {$file}\n");
        }

        Storage::disk('public')->put('demo/recruitment/photo-demo.jpg', 'Demo image placeholder');
    }
}

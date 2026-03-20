<?php

use App\Livewire\ApplicationManagement;
use App\Livewire\CandidateReview;
use App\Livewire\Candidate\Dashboard as CandidateDashboard;
use App\Livewire\Candidate\JobPortal;
use App\Livewire\EmailTemplateManagement;
use App\Livewire\JobApplications;
use App\Livewire\Candidate\MyApplications;
use App\Livewire\Candidate\MyProfile;
use App\Livewire\Candidate\ProfileSetup;
use App\Livewire\Dashboard;
use App\Livewire\DepartmentManagement;
use App\Livewire\JobManagement;
use App\Livewire\NewsManagement;
use App\Livewire\Frontend\ArticleDetail;
use App\Livewire\Frontend\ArticleList;
use App\Livewire\Frontend\CareerDetail;
use App\Livewire\Frontend\CareerList;
use App\Livewire\Frontend\Contact;
use App\Livewire\PtkManagement;
use App\Livewire\SiteManagement;
use App\Livewire\CandidateManagement;
use App\Livewire\InterviewManagement;
use App\Livewire\McuManagement;
use App\Livewire\OfferingLetterManagement;
use App\Livewire\OnboardingManagement;
use App\Livewire\PsychotestManagement;
use App\Livewire\Superadmin\SmtpSettings;
use App\Livewire\UserManagement;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/about', 'about')->name('about');
Route::get('/articles', ArticleList::class)->name('articles.index');
Route::get('/articles/{article:slug}', ArticleDetail::class)->name('articles.show');
Route::get('/careers', CareerList::class)->name('careers.index');
Route::get('/careers/{job}', CareerDetail::class)->name('careers.show');
Route::get('/contact', Contact::class)->name('contact');

Route::middleware(['auth'])->group(function () {
    // Profile setup (candidate only, before profile is complete)
    Route::get('/profile/setup', ProfileSetup::class)->name('candidate.profile.setup');

    Route::middleware(['candidate.profile.complete'])->group(function () {
        Route::get('dashboard', Dashboard::class)->name('dashboard');

        // Staff/Admin routes
        Route::get('/users', UserManagement::class)->name('users.index');
        Route::get('/news', NewsManagement::class)->name('news.index');
        Route::get('/jobs', JobManagement::class)->name('jobs.index');
        Route::get('/ptk', PtkManagement::class)->name('ptk.index');
        Route::get('/departments', DepartmentManagement::class)->name('departments.index');
        Route::get('/sites', SiteManagement::class)->name('sites.index');
        Route::get('/applications', JobApplications::class)->name('applications.index');
        Route::get('/applications/{job}', ApplicationManagement::class)->name('applications.job');
        Route::get('/applications/{job}/{application}', CandidateReview::class)->name('applications.review');
        Route::get('/kandidat/administrasi', CandidateManagement::class)->defaults('tab', 'administrasi')->name('candidates.administrasi');
        Route::get('/kandidat/on-progress', CandidateManagement::class)->defaults('tab', 'on-progress')->name('candidates.on-progress');
        Route::get('/kandidat/riwayat', CandidateManagement::class)->defaults('tab', 'riwayat')->name('candidates.riwayat');
        Route::get('/interview/hr', InterviewManagement::class)->defaults('tab', 'hr')->name('interviews.hr');
        Route::get('/interview/user', InterviewManagement::class)->defaults('tab', 'user')->name('interviews.user');
        Route::get('/offering-letter', OfferingLetterManagement::class)->name('offering.index');
        Route::get('/psychotest', PsychotestManagement::class)->name('psychotest.index');
        Route::get('/mcu', McuManagement::class)->name('mcu.index');
        Route::get('/onboarding', OnboardingManagement::class)->name('onboarding.index');
        Route::get('/email-templates', EmailTemplateManagement::class)->name('email-templates.index');
        Route::get('/superadmin/smtp-settings', SmtpSettings::class)->name('superadmin.smtp');

        // Candidate portal routes
        Route::get('/portal/dashboard', CandidateDashboard::class)->name('candidate.dashboard');
        Route::get('/portal', JobPortal::class)->name('candidate.portal');
        Route::get('/my-applications', MyApplications::class)->name('candidate.applications');
        Route::get('/my-profile', MyProfile::class)->name('candidate.profile');
    });
});

require __DIR__.'/settings.php';
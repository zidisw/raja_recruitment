# RAJA Recruitment System - Developer Guide

Welcome to the RAJA Recruitment System! This document is a comprehensive guide designed to help new developers understand the architecture, technology stack, and standard operating procedures for developing new features or modifying existing ones.

## 🛠️ Technology Stack

This project uses the **TALL Stack** with modern premium UI components:

* **T - Tailwind CSS:** For styling. Custom utility classes are in `resources/css/app.css` (like `.glass-card-static`, `.futuristic-bg`).
* **A - Alpine.js:** Used heavily alongside Livewire for lightweight frontend interactions (modals, dropdowns, interactions).
* **L - Laravel (v11+):** The core PHP framework handling routing, models, database interactions, and business logic.
* **L - Livewire (v3):** Used for building dynamic, reactive front-end interfaces without writing Vue/React. Almost every page is a Full-Page Livewire Component.
* **Flux UI:** A premium UI component library for Livewire. You'll see tags like `<flux:button>`, `<flux:sidebar>`, `<flux:modal>` everywhere.

---

## 📁 Key Directory Structure

When looking for files to edit, refer to these main directories:

* `app/Livewire/` - Contains the PHP logic for all pages/components (e.g., `CandidateManagement.php`).
* `resources/views/livewire/` - Contains the Blade HTML templates for the UI (e.g., `candidate-management.blade.php`).
* `resources/views/layouts/` - Contains wrapper layouts, including the main `app/sidebar.blade.php` navigation.
* `resources/views/components/` - Smaller reusable Blade components (like `custom-dropdown.blade.php`, `date-picker.blade.php`).
* `app/Models/` - Eloquent Database Models (e.g., `Interview`, `Application`, `Job`).
* `app/Enums/` - Strict PHP Enums used to track statuses securely (e.g., `RecruitmentStage.php`, `UserRole.php`).

---

## 🚀 How to Add a New Feature (Step-by-Step)

Imagine you need to add a brand new feature called **"Document Verification"** for candidates. Here is exactly how to do it end-to-end:

### Step 1: Database & Model (Backend)

1. **Create the Migration & Model:**

   ```bash
   php artisan make:model DocumentVerification -m
   ```

2. **Define the Schema:** Open `database/migrations/xxxx_create_document_verifications_table.php` and add your columns (e.g., `$table->foreignId('application_id')`, `$table->string('document_type')`).
3. **Run the Migration:**

   ```bash
   php artisan migrate
   ```

4. **Update the Model:** Open `app/Models/DocumentVerification.php` and add the `protected $fillable = [...]` attributes and relationships (like `public function application() { return $this->belongsTo(Application::class); }`).

### Step 2: Create the Livewire Component (Logic)

Generate the Livewire component that will handle the page's logic:

```bash
php artisan make:livewire DocumentVerificationManagement
```

This automatically creates two files:

1. `app/Livewire/DocumentVerificationManagement.php`
2. `resources/views/livewire/document-verification-management.blade.php`

**In the PHP file:**

* Set up authorization in the `mount()` method (e.g., `abort_unless(Auth::user()->canAccessRecruitment(), 403);`).
* Add the layout attribute: `#[Layout('layouts.app')]` above the class.
* Define variables (e.g., `public $documents;`) and methods (e.g., `public function approveDocument($id)`).

### Step 3: Build the UI (Frontend)

Open the generated `.blade.php` file and use Flux UI and Tailwind CSS to build the interface. Always stick to the established design language:

```blade
<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between">
        <flux:heading size="xl" level="1">{{ __('Verifikasi Dokumen') }}</flux:heading>
        <flux:button wire:click="exportData" variant="primary">Export List</flux:button>
    </div>

    <!-- Use the exact same table structures from other pages -->
    <div class="glass-card-static overflow-hidden p-0!">
        <table class="w-full text-sm modern-table">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">{{ __('Kandidat') }}</th>
                    <th class="text-center">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop your data here -->
            </tbody>
        </table>
    </div>
</div>
```

### Step 4: Add Routing

Register the new component in `routes/web.php` (or a specific route file if available) inside the authenticated middleware group:

```php
use App\Livewire\DocumentVerificationManagement;

Route::middleware(['auth', 'verified'])->group(function () {
    // Other routes...
    Route::get('/document-verification', DocumentVerificationManagement::class)->name('document.verification');
});
```

### Step 5: Add it to the Sidebar

Finally, expose the feature to users by adding it to the sidebar in `resources/views/layouts/app/sidebar.blade.php`.
Find the `Recruitment` section and append a new `<flux:sidebar.item>`:

```blade
<flux:sidebar.item icon="document-magnifying-glass" :href="route('document.verification')"
    :current="request()->routeIs('document.verification')" wire:navigate>
    {{ __('Verifikasi Dokumen') }}
</flux:sidebar.item>
```

---

## 🛠️ How to Modify Existing Features

Modifying existing logic requires knowing *where* the state is managed.

**Scenario: You want to add a new Stage into the Recruitment Pipeline (e.g., 'Background Check').**

1. **Update Enum:** Go to `app/Enums/RecruitmentStage.php` and add the new case (`case BACKGROUND_CHECK = 'background_check';`). Add the label/color mapping if required.
2. **Update Tracking Logs:** Ensure the Application model's Observers handle stage transitions if there's logging involved (e.g., logging stage changes).
3. **Update CandidateManagement Component:** If `CandidateManagement.php` filters candidates into tabs based on stages, update the query logic in the `$this->tab` conditions to include the new stage.
4. **Update Dropdowns:** Look through files like `candidate-management.blade.php` or `ptk-management.blade.php` and ensure the dropdowns pulling from `RecruitmentStage::cases()` map the new stage correctly.

**Scenario: A User reports a graphical bug (e.g., "The modal is clipping out of the screen").**

1. Identify the URL. Example: `/interview/hr`.
2. Go to `routes/web.php` to see which component serves that URL (`InterviewManagement::class`).
3. Open `resources/views/livewire/interview-management.blade.php`.
4. Locate the specific HTML element. Often graphical clips are caused by missing `overflow-visible`, `whitespace-nowrap` on tables, or a `z-index` conflict.
5. Apply Tailwind utilities to fix it directly in the Blade file.

---

## ✨ Standard Conventions & Best Practices

1. **Always Use `wire:navigate`**: When creating links (`<a>` or `<flux:button href="...">`), ALWAYS include the `wire:navigate` attribute. This makes page loads instant (SPA-feel) without full browser reloads.
2. **Global Toasts for Feedback**: Instead of raw JavaScript alerts, use the integrated Toast system to alert users after an action completes:

   ```php
   $this->dispatch('notify', message: __('Data saved successfully!'), type: 'success');
   ```

3. **Never Query Inside Blade Files**: Do not run database queries (like `User::all()`) directly inside `.blade.php` files. Always pass data from the `render()` method in your Livewire PHP class.
4. **Use Blade Components for Reusability**: If you re-use a block of HTML (like the custom dropdowns or date pickers), extract it into `resources/views/components/` and call it using `<x-date-picker />`.
5. **Localization (`__('text')`)**: Always wrap static texts inside the `__('...')` helper to support multi-language capabilities.

Happy Coding! 🚀

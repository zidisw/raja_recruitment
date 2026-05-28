<div class="flex flex-col gap-8">
    {{-- Modern Header --}}
    <div
        class="relative overflow-hidden rounded-2xl border-l-4 border-brand-500 bg-linear-to-br from-brand-50 via-blue-50 to-emerald-50 px-6 py-8 text-zinc-900 shadow-xl dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 dark:text-white sm:px-8">
        <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-brand-500/10 blur-2xl"></div>
        <div class="absolute -bottom-4 -left-4 h-32 w-32 rounded-full bg-brand-500/5 blur-2xl dark:bg-slate-500/10">
        </div>
        <div class="relative">
            <h1 class="text-2xl font-bold sm:text-3xl">{{ __('My Profile') }} 👤</h1>
            <p class="mt-2 text-zinc-600 dark:text-slate-300">{{ __('Manage your account and personal information') }}
            </p>
        </div>
    </div>

    {{-- Sidebar Layout --}}
    <div class="flex items-start max-md:flex-col lg:gap-8 gap-4">
        {{-- Left Sidebar --}}
        <div class="me-10 w-full pb-4 md:w-[220px]">
            <flux:navlist aria-label="Profile Sections" class="sticky top-8">
                <flux:navlist.group heading="Account">
                    <flux:navlist.item icon="user" wire:click="$set('activeTab', 'account')"
                        :current="$activeTab === 'account'">{{ __('Account Info') }}</flux:navlist.item>
                    <flux:navlist.item icon="key" wire:click="$set('activeTab', 'password')"
                        :current="$activeTab === 'password'">{{ __('Change Password') }}</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Profile Data" class="mt-4">
                    <flux:navlist.item icon="identification" wire:click="$set('activeTab', 'personal')"
                        :current="$activeTab === 'personal'">{{ __('Personal Info') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" wire:click="$set('activeTab', 'documents')"
                        :current="$activeTab === 'documents'">{{ __('Documents') }}</flux:navlist.item>
                    <flux:navlist.item icon="academic-cap" wire:click="$set('activeTab', 'education')"
                        :current="$activeTab === 'education'">{{ __('Education') }}</flux:navlist.item>
                    <flux:navlist.item icon="briefcase" wire:click="$set('activeTab', 'experience')"
                        :current="$activeTab === 'experience'">{{ __('Experience') }}</flux:navlist.item>
                    <flux:navlist.item icon="user-group" wire:click="$set('activeTab', 'organization')"
                        :current="$activeTab === 'organization'">{{ __('Organizations') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>
        </div>

        {{-- Main Content Window --}}
        <div class="flex-1 w-full max-w-3xl">

            {{-- Account Tab --}}
            @if ($activeTab === 'account')
                <div
                    class="theme-surface relative overflow-hidden rounded-2xl border border-zinc-200 bg-white/50 p-6 shadow-sm backdrop-blur-md dark:border-zinc-700/50 dark:bg-zinc-900/50 sm:p-8 animate-fade-in">
                    <div class="mb-6 flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-500/10 text-blue-600 dark:bg-blue-400/10 dark:text-blue-400">
                            <flux:icon.user class="size-5" />
                        </div>
                        <div>
                            <flux:heading size="lg">{{ __('Account Information') }}</flux:heading>
                            <flux:text class="text-sm text-zinc-500">{{ __('Update your login credentials') }}</flux:text>
                        </div>
                    </div>

                    @if ($this->hasUnverifiedEmail())
                        <flux:callout variant="warning" icon="exclamation-triangle" class="mb-6">
                            <flux:callout.heading>{{ __('Email not verified') }}</flux:callout.heading>
                            <flux:callout.text>{{ __('Please verify your new email address.') }}</flux:callout.text>
                        </flux:callout>
                    @endif

                    <form wire:submit="updateAccount" class="flex flex-col gap-4">
                        <flux:field>
                            <flux:label>{{ __('Full Name') }} *</flux:label>
                            <flux:input wire:model="name" />
                            <flux:error name="name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Email Address') }} *</flux:label>
                            <flux:input wire:model="email" type="email" />
                            <flux:error name="email" />
                        </flux:field>

                        <div class="flex justify-end mt-4">
                            <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('Save Changes') }}</span>
                                <span wire:loading>{{ __('Saving...') }}</span>
                            </flux:button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Change Password --}}
            @if ($activeTab === 'password')
                <div
                    class="theme-surface relative overflow-hidden rounded-2xl border border-zinc-200 bg-white/50 p-6 shadow-sm backdrop-blur-md dark:border-zinc-700/50 dark:bg-zinc-900/50 sm:p-8 animate-fade-in">
                    <div class="mb-6 flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-500/10 text-rose-600 dark:bg-rose-400/10 dark:text-rose-400">
                            <flux:icon.key class="size-5" />
                        </div>
                        <div>
                            <flux:heading size="lg">{{ __('Change Password') }}</flux:heading>
                            <flux:text class="text-sm text-zinc-500">
                                {{ __('Ensure your account is using a long, random password to stay secure') }}</flux:text>
                        </div>
                    </div>

                    <form wire:submit="updatePassword" class="flex flex-col gap-4">
                        <flux:field>
                            <flux:label>{{ __('Current Password') }} *</flux:label>
                            <flux:input wire:model="current_password" type="password" />
                            <flux:error name="current_password" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('New Password') }} *</flux:label>
                            <flux:input wire:model="password" type="password" />
                            <flux:error name="password" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Confirm New Password') }} *</flux:label>
                            <flux:input wire:model="password_confirmation" type="password" />
                            <flux:error name="password_confirmation" />
                        </flux:field>

                        <div class="flex justify-end mt-4">
                            <flux:button type="submit" variant="primary" icon="key" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('Update Password') }}</span>
                                <span wire:loading>{{ __('Updating...') }}</span>
                            </flux:button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Personal Data --}}
            @if ($activeTab === 'personal')
                <div
                    class="theme-surface relative overflow-hidden rounded-2xl border border-zinc-200 bg-white/50 p-6 shadow-sm backdrop-blur-md dark:border-zinc-700/50 dark:bg-zinc-900/50 sm:p-8 animate-fade-in">
                    <div class="mb-6 flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-600 dark:bg-indigo-400/10 dark:text-indigo-400">
                            <flux:icon.identification class="size-5" />
                        </div>
                        <div>
                            <flux:heading size="lg">{{ __('Personal Information') }}</flux:heading>
                            <flux:text class="text-sm text-zinc-500">{{ __('Update your basic identity details') }}
                            </flux:text>
                        </div>
                    </div>

                    <form wire:submit="savePersonalInfo" class="flex flex-col gap-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <flux:field class="sm:col-span-2">
                                <flux:label>{{ __('NIK (National ID Number)') }} *</flux:label>
                                <flux:input wire:model="nik" placeholder="16-digit NIK" maxlength="16" />
                                <flux:error name="nik" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Place of Birth') }} *</flux:label>
                                <flux:input wire:model="place_of_birth" />
                                <flux:error name="place_of_birth" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Date of Birth') }} *</flux:label>
                                <x-date-picker wire:model="date_of_birth" mode="date"
                                    placeholder="{{ __('Select date of birth...') }}" />
                                <flux:error name="date_of_birth" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Gender') }} *</flux:label>
                                <x-custom-select wire:model="gender" placeholder="{{ __('Select gender') }}" :options="['' => __('Select gender'), 'male' => __('Male'), 'female' => __('Female')]" />
                                <flux:error name="gender" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Religion') }} *</flux:label>
                                <x-custom-select wire:model="religion" placeholder="{{ __('Select religion') }}"
                                    :options="['' => __('Select religion'), 'Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu']" />
                                <flux:error name="religion" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Marital Status') }} *</flux:label>
                                <x-custom-select wire:model="marital_status" placeholder="{{ __('Select status') }}"
                                    :options="['' => __('Select status'), 'single' => __('Single'), 'married' => __('Married'), 'divorced' => __('Divorced'), 'widowed' => __('Widowed')]" />
                                <flux:error name="marital_status" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('WhatsApp Number') }} *</flux:label>
                                <flux:input wire:model="whatsapp" placeholder="e.g. 081234567890" />
                                <flux:error name="whatsapp" />
                            </flux:field>

                            <flux:field class="sm:col-span-2">
                                <flux:label>{{ __('KTP Address') }} *</flux:label>
                                <flux:textarea wire:model="address_ktp" rows="2" />
                                <flux:error name="address_ktp" />
                            </flux:field>

                            <flux:field class="sm:col-span-2">
                                <flux:label>{{ __('Domicile Address') }} *</flux:label>
                                <flux:textarea wire:model="address_domicile" rows="2" />
                                <flux:error name="address_domicile" />
                            </flux:field>

                            <flux:field class="sm:col-span-2">
                                <flux:label>{{ __('LinkedIn URL') }} <span
                                        class="text-zinc-400 font-normal text-xs">(optional)</span></flux:label>
                                <flux:input wire:model="linkedin_url" type="url"
                                    placeholder="https://linkedin.com/in/..." />
                                <flux:error name="linkedin_url" />
                            </flux:field>
                        </div>

                        <div class="flex justify-end mt-4">
                            <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('Save Personal Info') }}</span>
                                <span wire:loading>{{ __('Saving...') }}</span>
                            </flux:button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Documents --}}
            @if ($activeTab === 'documents')
                <div
                    class="theme-surface relative overflow-hidden rounded-2xl border border-zinc-200 bg-white/50 p-6 shadow-sm backdrop-blur-md dark:border-zinc-700/50 dark:bg-zinc-900/50 sm:p-8 animate-fade-in">
                    <div class="mb-6 flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                            <flux:icon.document-text class="size-5" />
                        </div>
                        <div>
                            <flux:heading size="lg">{{ __('Documents') }}</flux:heading>
                            <flux:text class="text-sm text-zinc-500">
                                {{ __('Upload new files to replace existing documents.') }}</flux:text>
                        </div>
                    </div>

                    <form wire:submit="saveDocuments" class="flex flex-col gap-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <flux:field>
                                <flux:label>{{ __('Profile Photo') }} <span
                                        class="text-zinc-400 font-normal text-xs">(optional)</span></flux:label>
                                @if ($profile?->photo_path)
                                    <p class="mb-1 text-xs text-zinc-400">{{ __('Current: ') }}<a
                                            href="{{ Storage::url($profile->photo_path) }}" target="_blank"
                                            class="text-blue-500 underline">{{ __('View') }}</a></p>
                                @endif
                                <flux:input wire:model="photo" type="file" accept="image/*" wire:key="profile-photo-file" />
                                <div wire:loading wire:target="photo" class="mt-1 text-xs text-brand-500">
                                    {{ __('Uploading...') }}
                                </div>
                                <flux:error name="photo" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('KTP Scan') }} <span
                                        class="text-zinc-400 font-normal text-xs">(optional)</span></flux:label>
                                @if ($profile?->ktp_path)
                                    <p class="mb-1 text-xs text-zinc-400">{{ __('Current: ') }}<a
                                            href="{{ Storage::url($profile->ktp_path) }}" target="_blank"
                                            class="text-blue-500 underline">{{ __('View') }}</a></p>
                                @endif
                                <flux:input wire:model="ktp_file" type="file" accept=".pdf,image/*" wire:key="profile-ktp-file" />
                                <div wire:loading wire:target="ktp_file" class="mt-1 text-xs text-brand-500">
                                    {{ __('Uploading...') }}
                                </div>
                                <flux:error name="ktp_file" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Portfolio') }} <span
                                        class="text-zinc-400 font-normal text-xs">(optional)</span></flux:label>
                                @if ($profile?->portfolio_path)
                                    <p class="mb-1 text-xs text-zinc-400">{{ __('Current: ') }}<a
                                            href="{{ Storage::url($profile->portfolio_path) }}" target="_blank"
                                            class="text-blue-500 underline">{{ __('View') }}</a></p>
                                @endif
                                <flux:input wire:model="portfolio" type="file" accept=".pdf" wire:key="profile-portfolio-file" />
                                <div wire:loading wire:target="portfolio" class="mt-1 text-xs text-brand-500">
                                    {{ __('Uploading...') }}
                                </div>
                                <flux:error name="portfolio" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Certificate') }} <span
                                        class="text-zinc-400 font-normal text-xs">(optional)</span></flux:label>
                                @if ($profile?->certificate_path)
                                    <p class="mb-1 text-xs text-zinc-400">{{ __('Current: ') }}<a
                                            href="{{ Storage::url($profile->certificate_path) }}" target="_blank"
                                            class="text-blue-500 underline">{{ __('View') }}</a></p>
                                @endif
                                <flux:input wire:model="certificate" type="file" accept=".pdf,image/*" wire:key="profile-certificate-file" />
                                <div wire:loading wire:target="certificate" class="mt-1 text-xs text-brand-500">
                                    {{ __('Uploading...') }}
                                </div>
                                <flux:error name="certificate" />
                            </flux:field>

                            <flux:field class="sm:col-span-2">
                                <flux:label>{{ __('Paklaring / Reference Letter') }} <span
                                        class="text-zinc-400 font-normal text-xs">(optional)</span></flux:label>
                                @if ($profile?->paklaring_path)
                                    <p class="mb-1 text-xs text-zinc-400">{{ __('Current: ') }}<a
                                            href="{{ Storage::url($profile->paklaring_path) }}" target="_blank"
                                            class="text-blue-500 underline">{{ __('View') }}</a></p>
                                @endif
                                <flux:input wire:model="paklaring" type="file" accept=".pdf,image/*" wire:key="profile-paklaring-file" />
                                <div wire:loading wire:target="paklaring" class="mt-1 text-xs text-brand-500">
                                    {{ __('Uploading...') }}
                                </div>
                                <flux:error name="paklaring" />
                            </flux:field>
                        </div>

                        <div class="flex justify-end mt-4">
                            <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled"
                                wire:target="saveDocuments,photo,ktp_file,portfolio,certificate,paklaring">
                                <span wire:loading.remove wire:target="saveDocuments">{{ __('Save Documents') }}</span>
                                <span wire:loading wire:target="saveDocuments">{{ __('Saving...') }}</span>
                            </flux:button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Education --}}
            @if ($activeTab === 'education')
                <div
                    class="theme-surface relative overflow-hidden rounded-2xl border border-zinc-200 bg-white/50 p-6 shadow-sm backdrop-blur-md dark:border-zinc-700/50 dark:bg-zinc-900/50 sm:p-8 animate-fade-in">
                    <form wire:submit="saveEducation" class="flex flex-col gap-4">
                        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-500/10 text-orange-600 dark:bg-orange-400/10 dark:text-orange-400">
                                    <flux:icon.academic-cap class="size-5" />
                                </div>
                                <div>
                                    <flux:heading size="lg">{{ __('Education') }}</flux:heading>
                                    <flux:text class="text-sm text-zinc-500">{{ __('Add your academic background') }}
                                    </flux:text>
                                </div>
                            </div>
                            <flux:button type="button" wire:click="addEducation" size="sm" variant="ghost" icon="plus"
                                class="self-start sm:self-center">
                                {{ __('Add') }}
                            </flux:button>
                        </div>

                        <div class="flex flex-col gap-4">
                            @foreach ($educations as $i => $edu)
                                <div wire:key="edu-{{ $i }}" class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                                    <div class="mb-3 flex items-center justify-between">
                                        <flux:heading size="sm">{{ __('Education') }} #{{ $i + 1 }}</flux:heading>
                                        @if (count($educations) > 1)
                                            <flux:button type="button" wire:click="removeEducation({{ $i }})"
                                                wire:target="removeEducation({{ $i }})" size="sm" variant="ghost" icon="trash"
                                                class="text-red-500" />
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <flux:field>
                                            <flux:label>{{ __('Degree') }} *</flux:label>
                                            <flux:select wire:model="educations.{{ $i }}.degree">
                                                <flux:select.option value="">{{ __('Select degree') }}</flux:select.option>
                                                <flux:select.option value="SD">SD</flux:select.option>
                                                <flux:select.option value="SMP">SMP</flux:select.option>
                                                <flux:select.option value="SMA/SMK">SMA/SMK</flux:select.option>
                                                <flux:select.option value="D1">D1</flux:select.option>
                                                <flux:select.option value="D2">D2</flux:select.option>
                                                <flux:select.option value="D3">D3</flux:select.option>
                                                <flux:select.option value="D4">D4</flux:select.option>
                                                <flux:select.option value="S1">S1</flux:select.option>
                                                <flux:select.option value="S2">S2</flux:select.option>
                                                <flux:select.option value="S3">S3</flux:select.option>
                                            </flux:select>
                                            <flux:error name="educations.{{ $i }}.degree" />
                                        </flux:field>
                                        <flux:field>
                                            <flux:label>{{ __('GPA') }}</flux:label>
                                            <flux:input wire:model="educations.{{ $i }}.gpa" type="number" step="0.01" min="0"
                                                max="4" placeholder="0.00" />
                                            <flux:error name="educations.{{ $i }}.gpa" />
                                        </flux:field>
                                        <flux:field class="sm:col-span-2">
                                            <flux:label>{{ __('Institution') }} *</flux:label>
                                            <flux:input wire:model="educations.{{ $i }}.institution_name" />
                                            <flux:error name="educations.{{ $i }}.institution_name" />
                                        </flux:field>
                                        <flux:field class="sm:col-span-2">
                                            <flux:label>{{ __('Major / Field of Study') }} *</flux:label>
                                            <flux:input wire:model="educations.{{ $i }}.major" />
                                            <flux:error name="educations.{{ $i }}.major" />
                                        </flux:field>
                                        <flux:field>
                                            <flux:label>{{ __('Start Year') }} *</flux:label>
                                            <flux:input wire:model="educations.{{ $i }}.start_year" type="number" min="1970"
                                                max="{{ date('Y') }}" />
                                            <flux:error name="educations.{{ $i }}.start_year" />
                                        </flux:field>
                                        <flux:field>
                                            <flux:label>{{ __('End Year') }}</flux:label>
                                            <flux:input wire:model="educations.{{ $i }}.end_year" type="number" min="1970"
                                                max="{{ date('Y') + 6 }}" />
                                            <flux:error name="educations.{{ $i }}.end_year" />
                                        </flux:field>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-end mt-4">
                            <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('Save Education') }}</span>
                                <span wire:loading>{{ __('Saving...') }}</span>
                            </flux:button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Work Experience --}}
            @if ($activeTab === 'experience')
                <div
                    class="theme-surface relative overflow-hidden rounded-2xl border border-zinc-200 bg-white/50 p-6 shadow-sm backdrop-blur-md dark:border-zinc-700/50 dark:bg-zinc-900/50 sm:p-8 animate-fade-in">
                    <form wire:submit="saveExperience" class="flex flex-col gap-4">
                        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-400">
                                    <flux:icon.briefcase class="size-5" />
                                </div>
                                <div>
                                    <flux:heading size="lg">{{ __('Work Experience') }}</flux:heading>
                                    <flux:text class="text-sm text-zinc-500">
                                        {{ __('Detail your professional employment history') }}</flux:text>
                                </div>
                            </div>
                            <flux:button type="button" wire:click="addExperience" size="sm" variant="ghost" icon="plus"
                                class="self-start sm:self-center">
                                {{ __('Add') }}
                            </flux:button>
                        </div>

                        <div class="flex flex-col gap-4">
                            @forelse ($experiences as $i => $exp)
                                <div wire:key="exp-{{ $i }}" class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                                    <div class="mb-3 flex items-center justify-between">
                                        <flux:heading size="sm">{{ __('Experience') }} #{{ $i + 1 }}</flux:heading>
                                        <flux:button type="button" wire:click="removeExperience({{ $i }})"
                                            wire:target="removeExperience({{ $i }})" size="sm" variant="ghost" icon="trash"
                                            class="text-red-500" />
                                    </div>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <flux:field class="sm:col-span-2">
                                            <flux:label>{{ __('Company Name') }} *</flux:label>
                                            <flux:input wire:model="experiences.{{ $i }}.company_name" />
                                            <flux:error name="experiences.{{ $i }}.company_name" />
                                        </flux:field>
                                        <flux:field class="sm:col-span-2">
                                            <flux:label>{{ __('Position') }} *</flux:label>
                                            <flux:input wire:model="experiences.{{ $i }}.position" />
                                            <flux:error name="experiences.{{ $i }}.position" />
                                        </flux:field>
                                        <flux:field>
                                            <flux:label>{{ __('Start Date') }} *</flux:label>
                                            <x-date-picker wire:model="experiences.{{ $i }}.start_date" mode="date"
                                                placeholder="{{ __('Start date') }}" />
                                            <flux:error name="experiences.{{ $i }}.start_date" />
                                        </flux:field>
                                        <flux:field>
                                            <flux:label>{{ __('End Date') }}</flux:label>
                                            <x-date-picker wire:model="experiences.{{ $i }}.end_date" mode="date"
                                                placeholder="{{ __('End date') }}" :disabled="$exp['is_current'] ?? false" />
                                            <flux:error name="experiences.{{ $i }}.end_date" />
                                        </flux:field>
                                        <flux:field class="sm:col-span-2">
                                            <flux:checkbox wire:model="experiences.{{ $i }}.is_current"
                                                label="{{ __('Currently working here') }}" />
                                        </flux:field>
                                        <flux:field>
                                            <flux:label>{{ __('Last Salary') }}</flux:label>
                                            <flux:input wire:model="experiences.{{ $i }}.last_salary" type="number" min="0" />
                                            <flux:error name="experiences.{{ $i }}.last_salary" />
                                        </flux:field>
                                        <flux:field class="sm:col-span-2">
                                            <flux:label>{{ __('Job Description') }}</flux:label>
                                            <flux:textarea wire:model="experiences.{{ $i }}.job_description" rows="3" />
                                            <flux:error name="experiences.{{ $i }}.job_description" />
                                        </flux:field>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-200 bg-zinc-50/50 py-8 px-4 text-center dark:border-zinc-700 dark:bg-zinc-800/50">
                                    <flux:icon.briefcase class="mb-2 size-8 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                        {{ __('No work experience added yet.') }}</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="flex justify-end mt-4">
                            <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('Save Experience') }}</span>
                                <span wire:loading>{{ __('Saving...') }}</span>
                            </flux:button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Organizations --}}
            @if ($activeTab === 'organization')
                <div
                    class="theme-surface relative overflow-hidden rounded-2xl border border-zinc-200 bg-white/50 p-6 shadow-sm backdrop-blur-md dark:border-zinc-700/50 dark:bg-zinc-900/50 sm:p-8 animate-fade-in">
                    <form wire:submit="saveOrganization" class="flex flex-col gap-4">
                        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-500/10 text-purple-600 dark:bg-purple-400/10 dark:text-purple-400">
                                    <flux:icon.user-group class="size-5" />
                                </div>
                                <div>
                                    <flux:heading size="lg">{{ __('Organizational Experience') }}</flux:heading>
                                    <flux:text class="text-sm text-zinc-500">
                                        {{ __('List your leadership and association roles') }}</flux:text>
                                </div>
                            </div>
                            <flux:button type="button" wire:click="addOrganization" size="sm" variant="ghost" icon="plus"
                                class="self-start sm:self-center">
                                {{ __('Add') }}
                            </flux:button>
                        </div>

                        <div class="flex flex-col gap-4">
                            @forelse ($organizations as $i => $org)
                                <div wire:key="org-{{ $i }}" class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                                    <div class="mb-3 flex items-center justify-between">
                                        <flux:heading size="sm">{{ __('Organization') }} #{{ $i + 1 }}</flux:heading>
                                        <flux:button type="button" wire:click="removeOrganization({{ $i }})"
                                            wire:target="removeOrganization({{ $i }})" size="sm" variant="ghost" icon="trash"
                                            class="text-red-500" />
                                    </div>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <flux:field class="sm:col-span-2">
                                            <flux:label>{{ __('Organization Name') }} *</flux:label>
                                            <flux:input wire:model="organizations.{{ $i }}.organization_name" />
                                            <flux:error name="organizations.{{ $i }}.organization_name" />
                                        </flux:field>
                                        <flux:field class="sm:col-span-2">
                                            <flux:label>{{ __('Position') }} *</flux:label>
                                            <flux:input wire:model="organizations.{{ $i }}.position" />
                                            <flux:error name="organizations.{{ $i }}.position" />
                                        </flux:field>
                                        <flux:field>
                                            <flux:label>{{ __('Start Date') }} *</flux:label>
                                            <x-date-picker wire:model="organizations.{{ $i }}.start_date" mode="date"
                                                placeholder="{{ __('Start date') }}" />
                                            <flux:error name="organizations.{{ $i }}.start_date" />
                                        </flux:field>
                                        <flux:field>
                                            <flux:label>{{ __('End Date') }}</flux:label>
                                            <x-date-picker wire:model="organizations.{{ $i }}.end_date" mode="date"
                                                placeholder="{{ __('End date') }}" :disabled="$org['is_current'] ?? false" />
                                            <flux:error name="organizations.{{ $i }}.end_date" />
                                        </flux:field>
                                        <flux:field class="sm:col-span-2">
                                            <flux:checkbox wire:model="organizations.{{ $i }}.is_current"
                                                label="{{ __('Currently active') }}" />
                                        </flux:field>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-200 bg-zinc-50/50 py-8 px-4 text-center dark:border-zinc-700 dark:bg-zinc-800/50">
                                    <flux:icon.user-group class="mb-2 size-8 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                        {{ __('No organizational experience added yet.') }}</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="flex justify-end mt-4">
                            <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('Save Organizations') }}</span>
                                <span wire:loading>{{ __('Saving...') }}</span>
                            </flux:button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>

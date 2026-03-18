<div class="flex flex-col gap-8">
    {{-- Modern Header --}}
    <div class="relative overflow-hidden rounded-2xl border-l-4 border-brand-500 bg-linear-to-br from-brand-50 via-blue-50 to-emerald-50 px-6 py-8 text-zinc-900 shadow-xl dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 dark:text-white sm:px-8">
        <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-brand-500/10 blur-2xl"></div>
        <div class="absolute -bottom-4 -left-4 h-32 w-32 rounded-full bg-brand-500/5 blur-2xl dark:bg-slate-500/10"></div>
        <div class="relative">
            <h1 class="text-2xl font-bold sm:text-3xl">{{ __('My Profile') }} 👤</h1>
            <p class="mt-2 text-zinc-600 dark:text-slate-300">{{ __('Manage your account and personal information') }}</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-zinc-200 dark:border-zinc-700">
        <button type="button" wire:click="$set('activeTab', 'account')"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px
                {{ $activeTab === 'account' ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
            <flux:icon.user class="size-4" />
            {{ __('Account') }}
        </button>
        <button type="button" wire:click="$set('activeTab', 'personal')"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px
                {{ $activeTab === 'personal' ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
            <flux:icon.identification class="size-4" />
            {{ __('Personal Data') }}
        </button>
    </div>

    {{-- Account Tab --}}
    @if ($activeTab === 'account')
        <div class="flex flex-col gap-8 max-w-xl">
            {{-- Account Info --}}
            <div class="flex flex-col gap-6">
                <flux:heading size="lg">{{ __('Account Information') }}</flux:heading>

                @if (session('account_success'))
                    <flux:callout variant="success" icon="check-circle">
                        <flux:callout.heading>{{ session('account_success') }}</flux:callout.heading>
                    </flux:callout>
                @endif

                @if ($this->hasUnverifiedEmail())
                    <flux:callout variant="warning" icon="exclamation-triangle">
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

                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary" icon="check">
                            {{ __('Save Changes') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            <flux:separator variant="subtle" />

            {{-- Change Password --}}
            <div class="flex flex-col gap-6">
                <flux:heading size="lg">{{ __('Change Password') }}</flux:heading>

                @if (session('password_success'))
                    <flux:callout variant="success" icon="check-circle">
                        <flux:callout.heading>{{ session('password_success') }}</flux:callout.heading>
                    </flux:callout>
                @endif

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

                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary" icon="key">
                            {{ __('Update Password') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Personal Data Tab --}}
    @if ($activeTab === 'personal')
        <form wire:submit="savePersonalData" class="flex flex-col gap-8">
            @if (session('personal_success'))
                <flux:callout variant="success" icon="check-circle">
                    <flux:callout.heading>{{ session('personal_success') }}</flux:callout.heading>
                </flux:callout>
            @endif

            {{-- Personal Info --}}
            <div class="flex flex-col gap-6">
                <flux:heading size="lg">{{ __('Personal Information') }}</flux:heading>

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
                        <x-date-picker wire:model="date_of_birth" mode="date" placeholder="{{ __('Select date of birth...') }}" />
                        <flux:error name="date_of_birth" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Gender') }} *</flux:label>
                        <x-custom-select
                            wire:model="gender"
                            placeholder="{{ __('Select gender') }}"
                            :options="['' => __('Select gender'), 'male' => __('Male'), 'female' => __('Female')]"
                        />
                        <flux:error name="gender" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Religion') }} *</flux:label>
                        <x-custom-select
                            wire:model="religion"
                            placeholder="{{ __('Select religion') }}"
                            :options="['' => __('Select religion'), 'Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu']"
                        />
                        <flux:error name="religion" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Marital Status') }} *</flux:label>
                        <x-custom-select
                            wire:model="marital_status"
                            placeholder="{{ __('Select status') }}"
                            :options="['' => __('Select status'), 'single' => __('Single'), 'married' => __('Married'), 'divorced' => __('Divorced'), 'widowed' => __('Widowed')]"
                        />
                        <flux:error name="marital_status" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('WhatsApp Number') }} *</flux:label>
                        <flux:input wire:model="whatsapp" placeholder="e.g. 081234567890" />
                        <flux:error name="whatsapp" />
                    </flux:field>

                    <flux:field class="sm:col-span-2">
                        <flux:label>{{ __('ID Card Address') }} *</flux:label>
                        <flux:textarea wire:model="address_ktp" rows="2" />
                        <flux:error name="address_ktp" />
                    </flux:field>

                    <flux:field class="sm:col-span-2">
                        <flux:label>{{ __('Domicile Address') }} *</flux:label>
                        <flux:textarea wire:model="address_domicile" rows="2" />
                        <flux:error name="address_domicile" />
                    </flux:field>

                    <flux:field class="sm:col-span-2">
                        <flux:label>{{ __('LinkedIn URL') }}</flux:label>
                        <flux:input wire:model="linkedin_url" type="url" placeholder="https://linkedin.com/in/..." />
                        <flux:error name="linkedin_url" />
                    </flux:field>
                </div>
            </div>

            <flux:separator variant="subtle" />

            {{-- Documents --}}
            <div class="flex flex-col gap-6">
                <flux:heading size="lg">{{ __('Documents') }}</flux:heading>
                <p class="text-sm text-zinc-500">{{ __('Upload new files to replace existing documents. Leave blank to keep current files.') }}</p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('Profile Photo') }}</flux:label>
                        @if ($profile?->photo_path)
                            <p class="mb-1 text-xs text-zinc-400">{{ __('Current: ') }}<a href="{{ Storage::url($profile->photo_path) }}" target="_blank" class="text-blue-500 underline">{{ __('View') }}</a></p>
                        @endif
                        <flux:input wire:model="photo" type="file" accept="image/*" />
                        <flux:error name="photo" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('ID Card Scan') }}</flux:label>
                        @if ($profile?->ktp_path)
                            <p class="mb-1 text-xs text-zinc-400">{{ __('Current: ') }}<a href="{{ Storage::url($profile->ktp_path) }}" target="_blank" class="text-blue-500 underline">{{ __('View') }}</a></p>
                        @endif
                        <flux:input wire:model="ktp_file" type="file" accept=".pdf,image/*" />
                        <flux:error name="ktp_file" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Portfolio') }}</flux:label>
                        @if ($profile?->portfolio_path)
                            <p class="mb-1 text-xs text-zinc-400">{{ __('Current: ') }}<a href="{{ Storage::url($profile->portfolio_path) }}" target="_blank" class="text-blue-500 underline">{{ __('View') }}</a></p>
                        @endif
                        <flux:input wire:model="portfolio" type="file" accept=".pdf" />
                        <flux:error name="portfolio" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Certificate') }}</flux:label>
                        @if ($profile?->certificate_path)
                            <p class="mb-1 text-xs text-zinc-400">{{ __('Current: ') }}<a href="{{ Storage::url($profile->certificate_path) }}" target="_blank" class="text-blue-500 underline">{{ __('View') }}</a></p>
                        @endif
                        <flux:input wire:model="certificate" type="file" accept=".pdf,image/*" />
                        <flux:error name="certificate" />
                    </flux:field>

                    <flux:field class="sm:col-span-2">
                        <flux:label>{{ __('Paklaring / Reference Letter') }}</flux:label>
                        @if ($profile?->paklaring_path)
                            <p class="mb-1 text-xs text-zinc-400">{{ __('Current: ') }}<a href="{{ Storage::url($profile->paklaring_path) }}" target="_blank" class="text-blue-500 underline">{{ __('View') }}</a></p>
                        @endif
                        <flux:input wire:model="paklaring" type="file" accept=".pdf,image/*" />
                        <flux:error name="paklaring" />
                    </flux:field>
                </div>
            </div>

            <flux:separator variant="subtle" />

            {{-- Education --}}
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">{{ __('Education') }}</flux:heading>
                    <flux:button type="button" wire:click="addEducation" size="sm" variant="ghost" icon="plus">
                        {{ __('Add') }}
                    </flux:button>
                </div>

                @foreach ($educations as $i => $edu)
                    <div wire:key="edu-{{ $i }}" class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="mb-3 flex items-center justify-between">
                            <flux:heading size="sm">{{ __('Education') }} #{{ $i + 1 }}</flux:heading>
                            @if (count($educations) > 1)
                                <flux:button type="button" wire:click="removeEducation({{ $i }})" size="sm" variant="ghost" icon="trash" class="text-red-500" />
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
                                <flux:input wire:model="educations.{{ $i }}.gpa" type="number" step="0.01" min="0" max="4" placeholder="0.00" />
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
                                <flux:input wire:model="educations.{{ $i }}.start_year" type="number" min="1970" max="{{ date('Y') }}" />
                                <flux:error name="educations.{{ $i }}.start_year" />
                            </flux:field>
                            <flux:field>
                                <flux:label>{{ __('End Year') }}</flux:label>
                                <flux:input wire:model="educations.{{ $i }}.end_year" type="number" min="1970" max="{{ date('Y') + 6 }}" />
                                <flux:error name="educations.{{ $i }}.end_year" />
                            </flux:field>
                        </div>
                    </div>
                @endforeach
            </div>

            <flux:separator variant="subtle" />

            {{-- Work Experience --}}
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">{{ __('Work Experience') }}</flux:heading>
                    <flux:button type="button" wire:click="addExperience" size="sm" variant="ghost" icon="plus">
                        {{ __('Add') }}
                    </flux:button>
                </div>

                @forelse ($experiences as $i => $exp)
                    <div wire:key="exp-{{ $i }}" class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="mb-3 flex items-center justify-between">
                            <flux:heading size="sm">{{ __('Experience') }} #{{ $i + 1 }}</flux:heading>
                            <flux:button type="button" wire:click="removeExperience({{ $i }})" size="sm" variant="ghost" icon="trash" class="text-red-500" />
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
                                <x-date-picker wire:model="experiences.{{ $i }}.start_date" mode="date" placeholder="{{ __('Start date') }}" />
                                <flux:error name="experiences.{{ $i }}.start_date" />
                            </flux:field>
                            <flux:field>
                                <flux:label>{{ __('End Date') }}</flux:label>
                                <x-date-picker wire:model="experiences.{{ $i }}.end_date" mode="date" placeholder="{{ __('End date') }}" :disabled="$exp['is_current'] ?? false" />
                                <flux:error name="experiences.{{ $i }}.end_date" />
                            </flux:field>
                            <flux:field class="sm:col-span-2">
                                <flux:checkbox wire:model="experiences.{{ $i }}.is_current" label="{{ __('Currently working here') }}" />
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
                    <p class="text-sm text-zinc-400">{{ __('No work experience added yet.') }}</p>
                @endforelse
            </div>

            <flux:separator variant="subtle" />

            {{-- Organizations --}}
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">{{ __('Organizational Experience') }}</flux:heading>
                    <flux:button type="button" wire:click="addOrganization" size="sm" variant="ghost" icon="plus">
                        {{ __('Add') }}
                    </flux:button>
                </div>

                @forelse ($organizations as $i => $org)
                    <div wire:key="org-{{ $i }}" class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="mb-3 flex items-center justify-between">
                            <flux:heading size="sm">{{ __('Organization') }} #{{ $i + 1 }}</flux:heading>
                            <flux:button type="button" wire:click="removeOrganization({{ $i }})" size="sm" variant="ghost" icon="trash" class="text-red-500" />
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
                                <x-date-picker wire:model="organizations.{{ $i }}.start_date" mode="date" placeholder="{{ __('Start date') }}" />
                                <flux:error name="organizations.{{ $i }}.start_date" />
                            </flux:field>
                            <flux:field>
                                <flux:label>{{ __('End Date') }}</flux:label>
                                <x-date-picker wire:model="organizations.{{ $i }}.end_date" mode="date" placeholder="{{ __('End date') }}" :disabled="$org['is_current'] ?? false" />
                                <flux:error name="organizations.{{ $i }}.end_date" />
                            </flux:field>
                            <flux:field class="sm:col-span-2">
                                <flux:checkbox wire:model="organizations.{{ $i }}.is_current" label="{{ __('Currently active') }}" />
                            </flux:field>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400">{{ __('No organizational experience added yet.') }}</p>
                @endforelse
            </div>

            {{-- Submit --}}
            <div class="flex justify-end gap-3 pt-4">
                <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Save All Changes') }}</span>
                    <span wire:loading>{{ __('Saving...') }}</span>
                </flux:button>
            </div>
        </form>
    @endif
</div>

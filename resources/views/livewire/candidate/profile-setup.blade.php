<div class="flex flex-col gap-8">
    {{-- Modern Header --}}
    <div
        class="relative overflow-hidden rounded-2xl border-l-4 border-brand-500 bg-linear-to-br from-brand-50 via-blue-50 to-emerald-50 px-6 py-8 text-zinc-900 shadow-xl dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 dark:text-white sm:px-8">
        <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-brand-500/10 blur-2xl"></div>
        <div class="absolute -bottom-4 -left-4 h-32 w-32 rounded-full bg-brand-500/5 blur-2xl dark:bg-slate-500/10">
        </div>
        <div class="relative">
            <h1 class="text-2xl font-bold sm:text-3xl">{{ __('Complete Your Profile') }} ✨</h1>
            <p class="mt-2 text-zinc-600 dark:text-slate-300">
                {{ __('Fill in your data to start applying for job openings') }}</p>
        </div>
    </div>

    {{-- Step Progress --}}
    <div
        class="rounded-xl border border-zinc-200/60 bg-white/70 px-4 py-5 backdrop-blur-sm dark:border-zinc-700/60 dark:bg-zinc-900/70">
        <div class="flex items-center gap-2">
            @foreach ([1 => 'Personal Data', 2 => 'Education', 3 => 'Experience', 4 => 'Organizations', 5 => 'Documents'] as $num => $label)
                <div class="flex items-center gap-2 {{ !$loop->first ? 'flex-1' : '' }}">
                    @if (!$loop->first)
                        <div
                            class="h-0.5 flex-1 rounded-full transition-colors {{ $step > $num - 1 ? 'bg-brand-500' : 'bg-zinc-200 dark:bg-zinc-700' }}">
                        </div>
                    @endif
                    <div class="flex flex-col items-center gap-1.5">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold transition-all
                                {{ $step > $num ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : ($step === $num ? 'bg-brand-500 text-white ring-4 ring-brand-500/20 shadow-md shadow-brand-500/20' : 'bg-zinc-200 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400') }}">
                            @if ($step > $num)
                                <flux:icon.check class="size-4" />
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        <span
                            class="hidden text-xs font-medium sm:block
                                {{ $step === $num ? 'text-brand-600 dark:text-brand-400' : ($step > $num ? 'text-brand-500' : 'text-zinc-400 dark:text-zinc-500') }}">
                            {{ $label }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Step 1: Personal Data --}}
    @if ($step === 1)
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
                    <flux:input wire:model="place_of_birth" placeholder="e.g. Jakarta" />
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
                    <x-custom-select wire:model="religion" placeholder="{{ __('Select religion') }}" :options="['' => __('Select religion'), 'Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu']" />
                    <flux:error name="religion" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Marital Status') }} *</flux:label>
                    <x-custom-select wire:model="marital_status" placeholder="{{ __('Select status') }}" :options="['' => __('Select status'), 'single' => __('Single'), 'married' => __('Married'), 'divorced' => __('Divorced'), 'widowed' => __('Widowed')]" />
                    <flux:error name="marital_status" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('WhatsApp Number') }} *</flux:label>
                    <flux:input wire:model="whatsapp" placeholder="e.g. 081234567890" />
                    <flux:error name="whatsapp" />
                </flux:field>

                <flux:field class="sm:col-span-2">
                    <flux:label>{{ __('ID Card Address') }} *</flux:label>
                    <flux:textarea wire:model="address_ktp" rows="2"
                        placeholder="Full address as on your national ID card" />
                    <flux:error name="address_ktp" />
                </flux:field>

                <flux:field class="sm:col-span-2">
                    <flux:label>{{ __('Domicile Address') }} *</flux:label>
                    <flux:textarea wire:model="address_domicile" rows="2" placeholder="Current residence address" />
                    <flux:error name="address_domicile" />
                </flux:field>

                <flux:field class="sm:col-span-2">
                    <flux:label>LinkedIn URL</flux:label>
                    <flux:input wire:model="linkedin_url" type="url" placeholder="https://linkedin.com/in/yourprofile" />
                    <flux:error name="linkedin_url" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Profile Photo') }} * <span class="text-zinc-400 font-normal text-xs">(max 2MB)</span>
                    </flux:label>
                    <input type="file" wire:model="photo" accept="image/*" class="block w-full text-sm text-zinc-500 dark:text-zinc-400
                                file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700
                                dark:file:bg-zinc-800 dark:file:text-zinc-300
                                hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700" />
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="mt-2 h-20 w-20 rounded-full object-cover" />
                    @endif
                    <flux:error name="photo" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('ID Card Scan') }} * <span class="text-zinc-400 font-normal text-xs">(PDF/image, max
                            2MB)</span></flux:label>
                    <input type="file" wire:model="ktp_file" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-zinc-500 dark:text-zinc-400
                                file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700
                                dark:file:bg-zinc-800 dark:file:text-zinc-300
                                hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700" />
                    <flux:error name="ktp_file" />
                </flux:field>
            </div>
        </div>
    @endif

    {{-- Step 2: Education --}}
    @if ($step === 2)
        <div class="flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">{{ __('Education History') }}</flux:heading>
                <flux:button wire:click="addEducation" variant="ghost" icon="plus" size="sm">
                    {{ __('Add Education') }}
                </flux:button>
            </div>

            @if (empty($educations))
                <div class="rounded-xl border border-dashed border-zinc-200 p-8 text-center dark:border-zinc-700">
                    <flux:text class="text-zinc-500">{{ __('No education added yet. Click "Add Education" to start.') }}
                    </flux:text>
                </div>
            @endif

            @foreach ($educations as $i => $edu)
                <div wire:key="edu-{{ $i }}" class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                    <div class="mb-4 flex items-center justify-between">
                        <flux:heading size="sm">{{ __('Education') }} #{{ $i + 1 }}</flux:heading>
                        @if (count($educations) > 1)
                            <flux:button wire:click="removeEducation({{ $i }})" variant="ghost" icon="trash" size="sm"
                                class="text-red-500 hover:text-red-600" />
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
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
                            <flux:label>{{ __('Institution Name') }} *</flux:label>
                            <flux:input wire:model="educations.{{ $i }}.institution_name"
                                placeholder="e.g. Universitas Indonesia" />
                            <flux:error name="educations.{{ $i }}.institution_name" />
                        </flux:field>

                        <flux:field class="sm:col-span-2">
                            <flux:label>{{ __('Major / Field of Study') }} *</flux:label>
                            <flux:input wire:model="educations.{{ $i }}.major" placeholder="e.g. Teknik Informatika" />
                            <flux:error name="educations.{{ $i }}.major" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Start Year') }} *</flux:label>
                            <flux:input wire:model="educations.{{ $i }}.start_year" type="number"
                                placeholder="{{ date('Y') - 4 }}" />
                            <flux:error name="educations.{{ $i }}.start_year" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('End Year') }} <span class="text-zinc-400 font-normal text-xs">(optional if
                                    ongoing)</span></flux:label>
                            <flux:input wire:model="educations.{{ $i }}.end_year" type="number" placeholder="{{ date('Y') }}" />
                            <flux:error name="educations.{{ $i }}.end_year" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('GPA') }} <span class="text-zinc-400 font-normal text-xs">(optional)</span>
                            </flux:label>
                            <flux:input wire:model="educations.{{ $i }}.gpa" type="number" step="0.01" min="0" max="4"
                                placeholder="e.g. 3.50" />
                            <flux:error name="educations.{{ $i }}.gpa" />
                        </flux:field>
                    </div>
                </div>
            @endforeach

            @error('educations')
                <flux:callout variant="danger" icon="x-circle">
                    <flux:callout.heading>{{ $message }}</flux:callout.heading>
                </flux:callout>
            @enderror
        </div>
    @endif

    {{-- Step 3: Work Experience --}}
    @if ($step === 3)
        <div class="flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg">{{ __('Work Experience') }}</flux:heading>
                    <flux:text class="text-zinc-500 text-sm mt-1">{{ __('Optional — skip if you are a fresh graduate') }}
                    </flux:text>
                </div>
                <flux:button wire:click="addExperience" variant="ghost" icon="plus" size="sm">
                    {{ __('Add Experience') }}
                </flux:button>
            </div>

            @if (empty($experiences))
                <div class="rounded-xl border border-dashed border-zinc-200 p-8 text-center dark:border-zinc-700">
                    <flux:text class="text-zinc-500">{{ __('No work experience added. You can continue to the next step.') }}
                    </flux:text>
                </div>
            @endif

            @foreach ($experiences as $i => $exp)
                <div wire:key="exp-{{ $i }}" class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                    <div class="mb-4 flex items-center justify-between">
                        <flux:heading size="sm">{{ __('Experience') }} #{{ $i + 1 }}</flux:heading>
                        <flux:button wire:click="removeExperience({{ $i }})" variant="ghost" icon="trash" size="sm"
                            class="text-red-500 hover:text-red-600" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Company Name') }} *</flux:label>
                            <flux:input wire:model="experiences.{{ $i }}.company_name" placeholder="e.g. PT. Contoh Jaya" />
                            <flux:error name="experiences.{{ $i }}.company_name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Position') }} *</flux:label>
                            <flux:input wire:model="experiences.{{ $i }}.position" placeholder="e.g. Operator Produksi" />
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
                                placeholder="{{ __('End date') }}" :disabled="$experiences[$i]['is_current'] ?? false" />
                            <flux:error name="experiences.{{ $i }}.end_date" />
                        </flux:field>

                        <flux:field class="sm:col-span-2">
                            <flux:checkbox wire:model="experiences.{{ $i }}.is_current"
                                label="{{ __('I currently work here') }}" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Last Salary (Rp)') }} <span
                                    class="text-zinc-400 font-normal text-xs">(optional)</span></flux:label>
                            <flux:input wire:model="experiences.{{ $i }}.last_salary" type="number" min="0"
                                placeholder="e.g. 5000000" />
                            <flux:error name="experiences.{{ $i }}.last_salary" />
                        </flux:field>

                        <flux:field class="sm:col-span-2">
                            <flux:label>{{ __('Job Description') }} <span
                                    class="text-zinc-400 font-normal text-xs">(optional)</span></flux:label>
                            <flux:textarea wire:model="experiences.{{ $i }}.job_description" rows="3"
                                placeholder="Briefly describe your main responsibilities..." />
                            <flux:error name="experiences.{{ $i }}.job_description" />
                        </flux:field>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Step 4: Organizations --}}
    @if ($step === 4)
        <div class="flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg">{{ __('Organization Experience') }}</flux:heading>
                    <flux:text class="text-zinc-500 text-sm mt-1">{{ __('Optional — add if applicable') }}</flux:text>
                </div>
                <flux:button wire:click="addOrganization" variant="ghost" icon="plus" size="sm">
                    {{ __('Add Organization') }}
                </flux:button>
            </div>

            @if (empty($organizations))
                <div class="rounded-xl border border-dashed border-zinc-200 p-8 text-center dark:border-zinc-700">
                    <flux:text class="text-zinc-500">{{ __('No organization added. You can continue to the next step.') }}
                    </flux:text>
                </div>
            @endif

            @foreach ($organizations as $i => $org)
                <div wire:key="org-{{ $i }}" class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                    <div class="mb-4 flex items-center justify-between">
                        <flux:heading size="sm">{{ __('Organization') }} #{{ $i + 1 }}</flux:heading>
                        <flux:button wire:click="removeOrganization({{ $i }})" variant="ghost" icon="trash" size="sm"
                            class="text-red-500 hover:text-red-600" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Organization Name') }} *</flux:label>
                            <flux:input wire:model="organizations.{{ $i }}.organization_name"
                                placeholder="e.g. BEM Universitas Indonesia" />
                            <flux:error name="organizations.{{ $i }}.organization_name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Position') }} *</flux:label>
                            <flux:input wire:model="organizations.{{ $i }}.position" placeholder="e.g. Ketua Divisi" />
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
                                placeholder="{{ __('End date') }}" :disabled="$organizations[$i]['is_current'] ?? false" />
                            <flux:error name="organizations.{{ $i }}.end_date" />
                        </flux:field>

                        <flux:field class="sm:col-span-2">
                            <flux:checkbox wire:model="organizations.{{ $i }}.is_current"
                                label="{{ __('Currently active in this organization') }}" />
                        </flux:field>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Step 5: Documents --}}
    @if ($step === 5)
        <div class="flex flex-col gap-6">
            <div>
                <flux:heading size="lg">{{ __('Supporting Documents') }}</flux:heading>
                <flux:text class="text-zinc-500 text-sm mt-1">
                    {{ __('All documents are optional but strengthen your application') }}</flux:text>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <flux:field>
                    <flux:label>{{ __('Portfolio') }} <span class="text-zinc-400 font-normal text-xs">(PDF, max 5MB)</span>
                    </flux:label>
                    <input type="file" wire:model="portfolio" accept=".pdf" class="block w-full text-sm text-zinc-500 dark:text-zinc-400
                                file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700
                                dark:file:bg-zinc-800 dark:file:text-zinc-300
                                hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700" />
                    <flux:error name="portfolio" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Certificate') }} <span class="text-zinc-400 font-normal text-xs">(PDF/image, max
                            5MB)</span></flux:label>
                    <input type="file" wire:model="certificate" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-zinc-500 dark:text-zinc-400
                                file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700
                                dark:file:bg-zinc-800 dark:file:text-zinc-300
                                hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700" />
                    <flux:error name="certificate" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Paklaring / Reference Letter') }} <span
                            class="text-zinc-400 font-normal text-xs">(PDF/image, max 5MB)</span></flux:label>
                    <input type="file" wire:model="paklaring" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-zinc-500 dark:text-zinc-400
                                file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700
                                dark:file:bg-zinc-800 dark:file:text-zinc-300
                                hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700" />
                    <flux:error name="paklaring" />
                </flux:field>
            </div>

            <flux:callout variant="info" icon="information-circle">
                <flux:callout.heading>{{ __('Almost there!') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('Click "Submit Profile" to complete your registration and access the job portal.') }}
                </flux:callout.text>
            </flux:callout>
        </div>
    @endif

    {{-- Navigation Buttons --}}
    <div class="flex items-center justify-between pt-2">
        <div>
            @if ($step > 1)
                <flux:button wire:click="previousStep" variant="ghost" icon="arrow-left">
                    {{ __('Previous') }}
                </flux:button>
            @endif
        </div>

        <div>
            @if ($step < $totalSteps)
                <flux:button wire:click="nextStep" variant="primary" icon-trailing="arrow-right">
                    {{ __('Next') }}
                </flux:button>
            @else
                <flux:button wire:click="save" variant="primary" icon="check" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ __('Submit Profile') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                </flux:button>
            @endif
        </div>
    </div>
</div>
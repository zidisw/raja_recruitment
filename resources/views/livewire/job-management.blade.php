<div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Job Management') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Manage job postings and vacancies') }}</flux:subheading>
        </div>

        <div class="flex gap-2">
            <flux:button wire:click="exportExcel" variant="ghost" icon="arrow-down-tray" class="w-full md:w-auto">
                {{ __('Export Excel') }}
            </flux:button>
            <flux:button wire:click="openCreate" variant="primary" icon="plus" class="w-full md:w-auto">
                {{ __('Post Job') }}
            </flux:button>
        </div>
    </div>

    <flux:separator variant="subtle" />

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>{{ session('success') }}</flux:callout.heading>
        </flux:callout>
    @endif

    {{-- Filters --}}
    <div class="flex flex-col gap-3">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <flux:field class="flex-1">
                <flux:input wire:model.live.debounce.300ms="searchTitle" placeholder="{{ __('Search job title...') }}"
                    icon="magnifying-glass" />
            </flux:field>
            @if (!$isHR)
                <div class="min-w-40">
                    <x-custom-select wire:model.live="filterDepartment" placeholder="{{ __('All Departments') }}"
                        :options="['' => __('All Departments')] + $departments->pluck('name', 'id')->toArray()" />
                </div>
            @endif
            <div class="min-w-36">
                <x-custom-select wire:model.live="filterSite" placeholder="{{ __('All Sites') }}" :options="['' => __('All Sites')] + $sites->pluck('name', 'id')->toArray()" />
            </div>
            <div class="min-w-32">
                <x-custom-select wire:model.live="filterLevel" placeholder="{{ __('All Levels') }}" :options="['' => __('All Levels'), 'staff' => 'Staff', 'non_staff' => 'Non-Staff']" />
            </div>
            <div class="min-w-32">
                <x-custom-select wire:model.live="filterStatus" placeholder="{{ __('All Statuses') }}" :options="['' => __('All Statuses'), 'active' => __('Active'), 'inactive' => __('Inactive')]" />
            </div>
        </div>
        <div class="flex items-center justify-end gap-2">
            <span class="text-sm text-zinc-500">{{ __('Per page:') }}</span>
            <div class="w-20">
                <x-custom-select wire:model.live="perPage" placeholder="10" :options="['10' => '10', '30' => '30', '50' => '50', '100' => '100']" />
            </div>
        </div>
    </div>

    @if ($jobs->isEmpty())
        <div
            class="flex flex-col items-center justify-center p-16 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
            <flux:icon.briefcase class="w-16 h-16 text-zinc-300 dark:text-zinc-600 mb-6" />
            <flux:heading size="lg" class="mb-2">{{ __('No Job Postings Yet') }}</flux:heading>
            <flux:text class="text-center max-w-md">
                {{ __('Click the button above to create your first job posting.') }}
            </flux:text>
        </div>
    @else
        <div class="glass-card-static overflow-hidden p-0!">
            <table class="w-full text-sm modern-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center!">{{ __('No.') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th class="hidden lg:table-cell">{{ __('Department') }}</th>
                        <th class="hidden lg:table-cell">{{ __('Site') }}</th>
                        <th class="hidden xl:table-cell">{{ __('PTK') }}</th>
                        <th class="text-center! hidden md:table-cell">{{ __('Level') }}</th>
                        <th class="text-center! hidden md:table-cell">{{ __('Applicants') }}</th>
                        <th class="text-center!">{{ __('Status') }}</th>
                        <th class="text-center!">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @foreach ($jobs as $job)
                        <tr wire:key="{{ $job->id }}" class="cursor-pointer">
                            <td class="px-4 py-3 text-center text-zinc-500 font-medium whitespace-nowrap">
                                {{ ($jobs->currentPage() - 1) * $jobs->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-zinc-900 dark:text-white">{{ $job->title }}</p>
                                @if ($job->closed_at)
                                    <p class="text-xs text-zinc-400 mt-0.5">
                                        {{ __('Closes') }}: {{ $job->closed_at->format('d M Y') }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400 hidden lg:table-cell">
                                {{ $job->department?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400 hidden lg:table-cell">
                                {{ $job->site?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400 hidden xl:table-cell">
                                {{ $job->ptk?->nomor_ptk ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-center hidden md:table-cell">
                                <flux:badge variant="outline" size="sm">
                                    {{ $job->level === \App\Enums\JobLevel::Staff ? 'Staff' : 'Non-Staff' }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 text-center hidden md:table-cell">
                                <flux:badge variant="outline">{{ $job->applications_count }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($job->is_active)
                                    <flux:badge color="green" size="sm">{{ __('Active') }}</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">{{ __('Inactive') }}</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <flux:button wire:click="toggleActive({{ $job->id }})" wire:target="toggleActive({{ $job->id }})" size="sm" variant="ghost"
                                        icon="{{ $job->is_active ? 'eye-slash' : 'eye' }}" class="app-action-btn" />
                                    <flux:button wire:click="openEdit({{ $job->id }})" wire:target="openEdit({{ $job->id }})" size="sm" variant="ghost" icon="pencil"
                                        class="app-action-btn" />
                                    <flux:button
                                        @click="$dispatch('confirm-action', {
                                            title: 'Hapus Job Posting?',
                                            description: 'Semua lamaran yang terkait juga akan ikut dihapus. Aksi ini tidak dapat dibatalkan.',
                                            variant: 'danger',
                                            method: 'delete',
                                            args: [{{ $job->id }}]
                                        })"
                                        size="sm" variant="ghost" icon="trash" class="app-action-btn-danger" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>{{ $jobs->links() }}</div>
    @endif

    <flux:modal wire:model="showModal" class="w-full max-w-3xl">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $editingId ? __('Edit Job Posting') : __('New Job Posting') }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('PTK Number') }} *</flux:label>
                    <x-custom-select wire:model="ptk_id" placeholder="{{ __('Select PTK') }}" :options="['' => __('Select PTK')] + $ptkList->mapWithKeys(fn($p) => [$p->id => $p->nomor_ptk . ' - ' . $p->posisi])->toArray()" :searchable="true" />
                    <flux:error name="ptk_id" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Job Title (From PTK Position)') }} *</flux:label>
                    <flux:input wire:model="title" readonly placeholder="{{ __('Auto-filled from PTK position') }}" />
                    <flux:description>{{ __('Title is generated automatically from selected PTK position.') }}
                    </flux:description>
                    <flux:error name="title" />
                </flux:field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Level') }} *</flux:label>
                        <x-custom-select wire:model="level" placeholder="{{ __('Select level...') }}" :options="['' => __('Select level...')] + collect($levels)->mapWithKeys(fn($l) => [$l->value => $l === \App\Enums\JobLevel::Staff ? 'Staff' : 'Non-Staff'])->toArray()" />
                        <flux:error name="level" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Closing Date') }}</flux:label>
                        <x-date-picker wire:model="closed_at" mode="date"
                            placeholder="{{ __('Select closing date...') }}" />
                        <flux:error name="closed_at" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if (!$isHR)
                        <flux:field>
                            <flux:label>{{ __('Department') }}</flux:label>
                            <x-custom-select wire:model="department_id" placeholder="{{ __('No department') }}"
                                :options="['' => __('No department')] + $departments->pluck('name', 'id')->toArray()"
                                :searchable="true" />
                            <flux:error name="department_id" />
                        </flux:field>
                    @endif

                    <flux:field>
                        <flux:label>{{ __('Site') }}</flux:label>
                        <x-custom-select wire:model="site_id" placeholder="{{ __('No site') }}" :options="['' => __('No site')] + $sites->pluck('name', 'id')->toArray()" :searchable="true" />
                        <flux:error name="site_id" />
                    </flux:field>
                </div>

                <flux:field>
                    <div class="flex items-center justify-between">
                        <flux:label>{{ __('Job Description') }} *</flux:label>
                        @if (!$editingId)
                            <button type="button"
                                wire:click="$set('description', &quot;Kami sedang mencari kandidat yang berkualitas untuk mengisi posisi ini.\n\nTanggung Jawab Utama:\n- [Tanggung jawab 1]\n- [Tanggung jawab 2]\n- [Tanggung jawab 3]\n\nLingkungan Kerja:\n- Bekerja di lingkungan yang profesional dan dinamis\n- Jam kerja: Senin-Jumat, 08:00-17:00\n- Lokasi: [Lokasi kerja]&quot;)"
                                class="text-xs text-brand-500 hover:text-brand-600 font-medium">
                                {{ __('📋 Load Template') }}
                            </button>
                        @endif
                    </div>
                    <flux:textarea wire:model="description" rows="8"
                        placeholder="Describe the role, responsibilities, and expectations..." />
                    <flux:error name="description" />
                </flux:field>

                <flux:field>
                    <div class="flex items-center justify-between">
                        <flux:label>{{ __('Requirements') }} *</flux:label>
                        @if (!$editingId)
                            <button type="button"
                                wire:click="$set('requirements', &quot;Kualifikasi:\n- Pendidikan minimal [SMA/D3/S1] jurusan [Jurusan]\n- Pengalaman minimal [X] tahun di bidang terkait\n- Usia maksimal [X] tahun\n\nKemampuan:\n- [Kemampuan teknis 1]\n- [Kemampuan teknis 2]\n- Mampu bekerja secara tim maupun individu\n- Komunikasi yang baik\n\nPersyaratan Dokumen:\n- CV / Resume terbaru\n- Ijazah & transkrip nilai\n- KTP yang masih berlaku\n- Sertifikat pendukung (jika ada)&quot;)"
                                class="text-xs text-brand-500 hover:text-brand-600 font-medium">
                                {{ __('📋 Load Template') }}
                            </button>
                        @endif
                    </div>
                    <flux:textarea wire:model="requirements" rows="6"
                        placeholder="List the required qualifications, experience, and skills..." />
                    <flux:error name="requirements" />
                </flux:field>

                <flux:field>
                    <div class="flex items-center justify-between">
                        <flux:label>{{ __('Benefits') }}</flux:label>
                        @if (!$editingId)
                            <button type="button"
                                wire:click="$set('benefits', &quot;- Makan 3 kali sehari\n- Mess / tempat tinggal\n- BPJS Kesehatan\n- BPJS Ketenagakerjaan\n- Tunjangan transportasi\n- Bonus kinerja&quot;)"
                                class="text-xs text-brand-500 hover:text-brand-600 font-medium">
                                {{ __('📋 Load Template') }}
                            </button>
                        @endif
                    </div>
                    <flux:textarea wire:model="benefits" rows="5"
                        placeholder="{{ __('List the benefits candidates will receive...') }}" />
                    <flux:error name="benefits" />
                </flux:field>

                <flux:field>
                    <div class="flex items-center gap-3">
                        <flux:checkbox wire:model="is_active" id="is_active" />
                        <flux:label for="is_active">{{ __('Active (visible to applicants)') }}</flux:label>
                    </div>
                </flux:field>

                <div class="flex justify-end gap-3 pt-2">
                    <flux:button type="button" variant="ghost" wire:click="$set('showModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? __('Update') : __('Post Job') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <x-confirm-action />
</div>

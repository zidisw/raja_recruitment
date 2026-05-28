<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('PTK') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Permintaan Tenaga Kerja — sumber untuk posting lowongan') }}
            </flux:subheading>
        </div>
        <flux:button wire:click="openCreate" variant="primary" icon="plus" class="w-full md:w-auto">
            {{ __('Tambah PTK') }}
        </flux:button>
    </div>

    <flux:separator variant="subtle" />

    {{-- Filters --}}
    <div class="flex flex-col gap-3">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <flux:field class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search PTK number, position, or department...') }}" icon="magnifying-glass" />
            </flux:field>
            <div class="min-w-40">
                <x-custom-select wire:model.live="filterStatus" placeholder="{{ __('All Statuses') }}" :options="['' => __('All Statuses'), 'draft' => 'Draft', 'approved' => 'Approved', 'closed' => 'Closed']" />
            </div>
        </div>
        <div class="flex items-center justify-end gap-2">
            <span class="text-sm text-zinc-500">{{ __('Per page:') }}</span>
            <div class="w-20">
                <x-custom-select wire:model.live="perPage" :options="['10' => '10', '30' => '30', '50' => '50', '100' => '100']" />
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="glass-card-static overflow-hidden p-0!">
        <div class="overflow-x-auto">
            <table class="w-full text-sm modern-table">
                <thead>
                    <tr>
                        <th class="text-center!">{{ __('No.') }}</th>
                        <th class="text-left!">{{ __('Nomor PTK') }}</th>
                        <th class="text-left!">{{ __('Posisi') }}</th>
                        <th class="hidden text-center! md:table-cell">{{ __('Dibuat Pada') }}</th>
                        <th class="hidden text-center! lg:table-cell">{{ __('Dibuat Oleh') }}</th>
                        <th class="text-center!">{{ __('Lampiran') }}</th>
                        <th class="text-center!">{{ __('Status') }}</th>
                        <th class="text-center! whitespace-nowrap w-px">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse ($ptkItems as $item)
                        <tr wire:key="ptk-row-{{ $item->id }}" class="cursor-pointer">
                            <td class="px-4 py-4 text-center text-zinc-500 font-medium">
                                {{ ($ptkItems->currentPage() - 1) * $ptkItems->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 align-middle text-left">
                                <p class="font-semibold text-zinc-900 dark:text-white">{{ $item->nomor_ptk }}</p>
                                @if ($item->department)
                                    <p class="text-xs text-zinc-400 mt-0.5">{{ $item->department }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-middle text-left text-zinc-700 dark:text-zinc-300">
                                {{ $item->posisi }}
                            </td>
                            <td class="hidden px-6 py-4 align-middle text-center md:table-cell">
                                <p class="text-xs text-zinc-600 dark:text-zinc-300">
                                    {{ $item->created_at->format('d M Y') }}
                                </p>
                                <p class="text-xs text-zinc-400">{{ $item->created_at->format('H:i') }}</p>
                            </td>
                            <td class="hidden px-6 py-4 align-middle text-center lg:table-cell">
                                @if ($item->createdBy)
                                    <span
                                        class="text-xs font-medium text-zinc-700 dark:text-zinc-300">{{ $item->createdBy->name }}</span>
                                @else
                                    <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-middle text-center">
                                @if ($item->attachment_path)
                                    <div class="flex justify-center">
                                        <a href="{{ Storage::url($item->attachment_path) }}" target="_blank"
                                            class="inline-flex items-center gap-1 text-xs text-brand-500 hover:text-brand-600 dark:hover:text-brand-400 font-medium">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Lihat
                                        </a>
                                    </div>
                                @else
                                    <span class="text-zinc-300 dark:text-zinc-600 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-middle text-center">
                                @php
                                    $statusColor = match ($item->status) {
                                        'approved' => 'text-green-700 bg-green-50 dark:text-green-400 dark:bg-green-500/10',
                                        'closed' => 'text-red-700 bg-red-50 dark:text-red-400 dark:bg-red-500/10',
                                        default => 'text-zinc-600 bg-zinc-100 dark:text-zinc-400 dark:bg-zinc-800',
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ strtoupper($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-middle text-center whitespace-nowrap w-px">
                                <div class="inline-flex flex-nowrap justify-center gap-2 whitespace-nowrap">
                                    <flux:button wire:click="openEdit({{ $item->id }})"
                                        wire:target="openEdit({{ $item->id }})" size="sm" variant="ghost" icon="pencil"
                                        class="app-action-btn">{{ __('Edit') }}
                                    </flux:button>
                                    <flux:button @click="$dispatch('confirm-action', {
                                                    title: 'Hapus PTK?',
                                                    description: 'PTK ini beserta lampirannya akan dihapus. Aksi ini tidak dapat dibatalkan.',
                                                    variant: 'danger',
                                                    method: 'delete',
                                                    args: [{{ $item->id }}]
                                                })" size="sm" variant="ghost" icon="trash" class="app-action-btn-danger">
                                        {{ __('Hapus') }}
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-14 text-center">
                                <div class="flex flex-col items-center gap-3 text-zinc-400">
                                    <flux:icon.clipboard-document-list class="w-10 h-10 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-sm">{{ __('Belum ada data PTK.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $ptkItems->links() }}</div>

    {{-- ─── Modal ─── --}}
    <flux:modal wire:model="showModal" class="w-full max-w-xl">
        <div class="space-y-6">

            {{-- ── STEP 1: Pilih mode ── --}}
            @if ($mode === '')
                <div>
                    <flux:heading size="lg">{{ __('Tambah PTK') }}</flux:heading>
                    <flux:text class="mt-1 text-zinc-500 dark:text-zinc-400">
                        {{ __('Pilih cara menambahkan Permintaan Tenaga Kerja') }}
                    </flux:text>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Upload Card --}}
                    <button wire:click="setMode('upload')" type="button"
                        class="group flex flex-col items-start gap-4 p-5 rounded-xl border-2 border-dashed border-zinc-200 dark:border-zinc-700 hover:border-brand-400 dark:hover:border-brand-500 hover:bg-brand-50/50 dark:hover:bg-brand-500/5 transition-all duration-200 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                        <div
                            class="w-11 h-11 rounded-xl bg-amber-100 dark:bg-amber-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-zinc-900 dark:text-white">{{ __('Upload PTK') }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">
                                {{ __('Upload foto atau PDF dari PTK fisik. Cukup isi nomor & posisi.') }}
                            </p>
                        </div>
                        <span
                            class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400">
                            Foto / PDF
                        </span>
                    </button>

                    {{-- Create Card --}}
                    <button wire:click="setMode('create')" type="button"
                        class="group flex flex-col items-start gap-4 p-5 rounded-xl border-2 border-dashed border-zinc-200 dark:border-zinc-700 hover:border-brand-400 dark:hover:border-brand-500 hover:bg-brand-50/50 dark:hover:bg-brand-500/5 transition-all duration-200 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                        <div class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-zinc-900 dark:text-white">{{ __('Buat PTK') }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">
                                {{ __('Isi form digital lengkap untuk membuat PTK baru secara langsung.') }}
                            </p>
                        </div>
                        <span
                            class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400">
                            Form Lengkap
                        </span>
                    </button>
                </div>

                <div class="flex justify-end">
                    <flux:button variant="ghost" wire:click="$set('showModal', false)">
                        {{ __('Batal') }}
                    </flux:button>
                </div>

                {{-- ── STEP 2a: Upload Form ── --}}
            @elseif ($mode === 'upload')
                <div class="flex items-center gap-3">
                    @if (!$editingId)
                        <button wire:click="setMode('')" type="button"
                            class="p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                    @endif
                    <div>
                        <flux:heading size="lg">
                            {{ $editingId ? __('Edit PTK') : __('Upload PTK') }}
                        </flux:heading>
                        <flux:text class="text-xs text-zinc-400 mt-0.5">
                            {{ __('Upload foto atau PDF dari PTK fisik') }}
                        </flux:text>
                    </div>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Nomor PTK') }}</flux:label>
                        <flux:input wire:model="nomor_ptk" placeholder="{{ __('Contoh: PTK/HRD/2026/001') }}" />
                        <flux:error name="nomor_ptk" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Posisi / Jabatan') }}</flux:label>
                        <flux:input wire:model="posisi" placeholder="{{ __('Contoh: Operator Alat Berat') }}" />
                        <flux:error name="posisi" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Status') }}</flux:label>
                        <x-custom-select wire:model="status" :options="['draft' => 'Draft', 'approved' => 'Approved', 'closed' => 'Closed']" />
                        <flux:error name="status" />
                    </flux:field>

                    <flux:field>
                        <flux:label>
                            {{ __('Lampiran PTK') }}
                            @if (!$editingId)
                                <span class="text-red-500 ml-0.5">*</span>
                            @endif
                            <span class="text-zinc-400 dark:text-zinc-500 text-xs font-normal ml-2">JPG, PNG, PDF — maks
                                5 MB</span>
                        </flux:label>

                        @if ($editingId && $existingAttachmentPath)
                            <div
                                class="flex items-center gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-sm mb-3">
                                <svg class="w-4 h-4 text-zinc-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <span class="text-zinc-600 dark:text-zinc-300 flex-1 truncate text-xs">Lampiran
                                    tersimpan</span>
                                <a href="{{ Storage::url($existingAttachmentPath) }}" target="_blank"
                                    class="text-brand-500 hover:text-brand-600 text-xs font-semibold shrink-0">Lihat</a>
                            </div>
                            <p class="text-xs text-zinc-400 mb-2">Upload file baru untuk menggantikan</p>
                        @endif

                        <input type="file" wire:model="attachment" wire:key="ptk-attachment-{{ $editingId ?? 'new' }}"
                            accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm text-zinc-500 dark:text-zinc-400
                                                           file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                                           file:text-sm file:font-semibold
                                                           file:bg-zinc-100 file:text-zinc-700
                                                           hover:file:bg-zinc-200
                                                           dark:file:bg-zinc-700 dark:file:text-zinc-300
                                                           dark:hover:file:bg-zinc-600
                                                           cursor-pointer mt-1">

                        <div wire:loading wire:target="attachment"
                            class="flex items-center gap-1.5 text-xs text-zinc-400 mt-2">
                            <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            Mengunggah...
                        </div>
                        <flux:error name="attachment" />
                    </flux:field>

                    <div class="flex justify-end gap-3 pt-2">
                        <flux:button type="button" variant="ghost" wire:click="$set('showModal', false)">
                            {{ __('Batal') }}
                        </flux:button>
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                            wire:target="save,attachment">
                            <span wire:loading.remove wire:target="save">{{ __('Simpan') }}</span>
                            <span wire:loading wire:target="save">{{ __('Menyimpan...') }}</span>
                        </flux:button>
                    </div>
                </form>

                {{-- ── STEP 2b: Create (Full) Form ── --}}
            @elseif ($mode === 'create')
                <div class="flex items-center gap-3">
                    @if (!$editingId)
                        <button wire:click="setMode('')" type="button"
                            class="p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                    @endif
                    <flux:heading size="lg">
                        {{ $editingId ? __('Edit PTK') : __('Buat PTK') }}
                    </flux:heading>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Nomor PTK') }}</flux:label>
                            <flux:input wire:model="nomor_ptk" placeholder="PTK/HRD/2026/001" />
                            <flux:error name="nomor_ptk" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Tanggal Permintaan') }}</flux:label>
                            <x-date-picker wire:model="tanggal_permintaan" mode="date"
                                placeholder="{{ __('Pilih tanggal...') }}" />
                            <flux:error name="tanggal_permintaan" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Department') }}</flux:label>
                            <flux:input wire:model="department" />
                            <flux:error name="department" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Posisi') }}</flux:label>
                            <flux:input wire:model="posisi" />
                            <flux:error name="posisi" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Jumlah Kebutuhan') }}</flux:label>
                            <flux:input type="number" min="1" wire:model="jumlah_kebutuhan" />
                            <flux:error name="jumlah_kebutuhan" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Status') }}</flux:label>
                            <x-custom-select wire:model="status" :options="['draft' => 'Draft', 'approved' => 'Approved', 'closed' => 'Closed']" />
                            <flux:error name="status" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Alasan Permintaan') }}</flux:label>
                        <flux:textarea rows="3" wire:model="alasan_permintaan" />
                        <flux:error name="alasan_permintaan" />
                    </flux:field>

                    <div class="flex justify-end gap-3 pt-2">
                        <flux:button type="button" variant="ghost" wire:click="$set('showModal', false)">
                            {{ __('Batal') }}
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ __('Simpan') }}
                        </flux:button>
                    </div>
                </form>
            @endif

        </div>
    </flux:modal>

    <x-confirm-action />
</div>

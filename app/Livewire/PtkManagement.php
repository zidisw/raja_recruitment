<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\Ptk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PtkManagement extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public int $perPage = 10;
    public bool $showModal = false;
    public ?int $editingId = null;

    /** '' = pilih mode, 'upload' = upload PTK fisik, 'create' = buat PTK digital */
    public string $mode = '';

    // Shared fields
    public string $nomor_ptk = '';
    public string $posisi = '';
    public string $status = 'draft';

    // Create-only fields
    public string $department = '';
    public int $jumlah_kebutuhan = 1;
    public string $alasan_permintaan = '';
    public string $tanggal_permintaan = '';

    // Upload-only
    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $attachment = null;
    public ?string $existingAttachmentPath = null;

    public function mount(): void
    {
        abort_unless(Auth::user()->canAccessRecruitment(), 403);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function openEdit(Ptk $ptk): void
    {
        $this->editingId = $ptk->id;
        $this->nomor_ptk = $ptk->nomor_ptk;
        $this->posisi = $ptk->posisi;
        $this->department = (string) $ptk->department;
        $this->jumlah_kebutuhan = $ptk->jumlah_kebutuhan;
        $this->alasan_permintaan = (string) $ptk->alasan_permintaan;
        $this->tanggal_permintaan = $ptk->tanggal_permintaan?->format('Y-m-d') ?? '';
        $this->status = $ptk->status;
        $this->existingAttachmentPath = $ptk->attachment_path;
        $this->mode = $ptk->attachment_path ? 'upload' : 'create';
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->mode === 'upload') {
            $this->validate([
                'nomor_ptk'  => ['required', 'string', 'max:255', 'unique:ptk,nomor_ptk,' . $this->editingId],
                'posisi'     => ['required', 'string', 'max:255'],
                'status'     => ['required', 'in:draft,approved,closed'],
                'attachment' => [$this->editingId ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            ]);

            $data = [
                'nomor_ptk' => $this->nomor_ptk,
                'posisi'    => $this->posisi,
                'status'    => $this->status,
            ];

            if ($this->attachment) {
                if ($this->existingAttachmentPath) {
                    Storage::disk('public')->delete($this->existingAttachmentPath);
                }
                $data['attachment_path'] = $this->attachment->store('ptk-attachments', 'public');
            }
        } else {
            $this->validate([
                'nomor_ptk'          => ['required', 'string', 'max:255', 'unique:ptk,nomor_ptk,' . $this->editingId],
                'department'         => ['required', 'string', 'max:255'],
                'posisi'             => ['required', 'string', 'max:255'],
                'jumlah_kebutuhan'   => ['required', 'integer', 'min:1'],
                'alasan_permintaan'  => ['nullable', 'string'],
                'tanggal_permintaan' => ['required', 'date'],
                'status'             => ['required', 'in:draft,approved,closed'],
            ]);

            $data = [
                'nomor_ptk'          => $this->nomor_ptk,
                'department'         => $this->department,
                'posisi'             => $this->posisi,
                'jumlah_kebutuhan'   => $this->jumlah_kebutuhan,
                'alasan_permintaan'  => $this->alasan_permintaan,
                'tanggal_permintaan' => $this->tanggal_permintaan,
                'status'             => $this->status,
            ];
        }

        if ($this->editingId) {
            Ptk::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', ['message' => __('PTK berhasil diperbarui.'), 'type' => 'success']);
        } else {
            $data['created_by'] = Auth::id();
            Ptk::create($data);
            $this->dispatch('notify', ['message' => __('PTK berhasil ditambahkan.'), 'type' => 'success']);
        }

        $this->resetForm();
        $this->showModal = false;
    }

    public function delete(Ptk $ptk): void
    {
        if ($ptk->jobs()->exists()) {
            $this->dispatch('notify', ['message' => __('PTK tidak dapat dihapus karena sudah digunakan di lowongan.'), 'type' => 'error']);

            return;
        }

        if ($ptk->attachment_path) {
            Storage::disk('public')->delete($ptk->attachment_path);
        }

        $ptk->delete();
        $this->dispatch('notify', ['message' => __('PTK berhasil dihapus.'), 'type' => 'success']);
    }

    public function render(): \Illuminate\View\View
    {
        $query = Ptk::with('createdBy')->latest();

        if ($this->search !== '') {
            $query->whereAny(['nomor_ptk', 'posisi', 'department'], 'like', '%' . $this->search . '%');
        }

        return view('livewire.ptk-management', [
            'ptkItems' => $query->paginate($this->perPage),
        ]);
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->mode = '';
        $this->nomor_ptk = '';
        $this->posisi = '';
        $this->department = '';
        $this->alasan_permintaan = '';
        $this->tanggal_permintaan = '';
        $this->attachment = null;
        $this->existingAttachmentPath = null;
        $this->jumlah_kebutuhan = 1;
        $this->status = 'draft';
    }
}

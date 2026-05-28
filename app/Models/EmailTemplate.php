<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RecruitmentStage;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    public const JOB_LEVELS = ['staff', 'non_staff'];

    protected $fillable = [
        'stage',
        'job_level',
        'subject',
        'body',
    ];

    protected function casts(): array
    {
        return [
            'stage' => RecruitmentStage::class,
        ];
    }

    /**
     * @return array{subject: string, body: string}
     */
    public static function defaultFor(RecruitmentStage $stage): array
    {
        return [
            'subject' => self::defaultSubject($stage),
            'body' => self::defaultBody($stage),
        ];
    }

    public static function defaultSubject(RecruitmentStage $stage): string
    {
        return match ($stage) {
            RecruitmentStage::ADMINISTRASI => 'Hasil Seleksi Administrasi - {job}',
            RecruitmentStage::HR_INTERVIEW => 'Jadwal Interview HR - {job}',
            RecruitmentStage::USER_INTERVIEW => 'Jadwal Interview User - {job}',
            RecruitmentStage::OFFERING => 'Offering Letter - {job}',
            RecruitmentStage::PSYCHOTEST => 'Informasi Psikotes - {job}',
            RecruitmentStage::MCU => 'Informasi Medical Check Up - {job}',
            RecruitmentStage::ONBOARDING => 'Informasi Onboarding - {job}',
            RecruitmentStage::HIRED => 'Selamat, Anda Diterima - {job}',
            RecruitmentStage::REJECTED => 'Pembaruan Hasil Seleksi - {job}',
            default => 'Pembaruan Lamaran - {job}',
        };
    }

    public static function defaultBody(RecruitmentStage $stage): string
    {
        return match ($stage) {
            RecruitmentStage::ADMINISTRASI => self::body([
                'Yth. {name},',
                'Terima kasih atas minat Anda untuk bergabung dengan PT Roda Jaya Sakti.',
                'Kami informasikan bahwa lamaran Anda untuk posisi {job} telah lolos seleksi administrasi dan akan masuk ke tahap Interview HR.',
                'Silakan masuk ke portal kandidat untuk memantau jadwal dan instruksi berikutnya.',
                'Hormat kami,',
                'Tim Rekrutmen PT Roda Jaya Sakti',
            ]),
            RecruitmentStage::HR_INTERVIEW => self::body([
                'Yth. {name},',
                'Kami informasikan bahwa Anda dijadwalkan mengikuti Interview HR untuk posisi {job}.',
                'Silakan masuk ke portal kandidat untuk melihat detail jadwal, lokasi atau tautan interview, serta instruksi yang perlu dipersiapkan.',
                'Mohon hadir sesuai jadwal dan memastikan data kontak Anda tetap aktif.',
                'Hormat kami,',
                'Tim Rekrutmen PT Roda Jaya Sakti',
            ]),
            RecruitmentStage::USER_INTERVIEW => self::body([
                'Yth. {name},',
                'Selamat, Anda melanjutkan proses seleksi untuk posisi {job} ke tahap Interview User.',
                'Detail jadwal, lokasi atau tautan interview, serta informasi pendukung dapat dilihat melalui portal kandidat.',
                'Mohon mempersiapkan diri dan hadir sesuai jadwal yang telah ditentukan.',
                'Hormat kami,',
                'Tim Rekrutmen PT Roda Jaya Sakti',
            ]),
            RecruitmentStage::OFFERING => self::body([
                'Yth. {name},',
                'Kami informasikan bahwa Offering Letter untuk posisi {job} telah tersedia di portal kandidat.',
                'Silakan membaca dokumen tersebut dengan saksama. Jika Anda menyetujui penawaran, unggah kembali Offering Letter yang telah ditandatangani melalui portal kandidat.',
                'Apabila terdapat pertanyaan, silakan hubungi Tim Rekrutmen melalui kanal komunikasi resmi.',
                'Hormat kami,',
                'Tim Rekrutmen PT Roda Jaya Sakti',
            ]),
            RecruitmentStage::PSYCHOTEST => self::body([
                'Yth. {name},',
                'Kami informasikan bahwa proses seleksi Anda untuk posisi {job} masuk ke tahap Psikotes.',
                'Silakan masuk ke portal kandidat untuk melihat jadwal, instruksi, dan dokumen pendukung yang perlu disiapkan.',
                'Mohon mengikuti seluruh instruksi agar proses seleksi dapat berjalan lancar.',
                'Hormat kami,',
                'Tim Rekrutmen PT Roda Jaya Sakti',
            ]),
            RecruitmentStage::MCU => self::body([
                'Yth. {name},',
                'Kami informasikan bahwa proses seleksi Anda untuk posisi {job} masuk ke tahap Medical Check Up.',
                'Silakan masuk ke portal kandidat untuk melihat jadwal, lokasi, dan instruksi pemeriksaan yang perlu diperhatikan.',
                'Mohon membawa dokumen yang diminta dan hadir sesuai jadwal.',
                'Hormat kami,',
                'Tim Rekrutmen PT Roda Jaya Sakti',
            ]),
            RecruitmentStage::ONBOARDING => self::body([
                'Yth. {name},',
                'Selamat, proses rekrutmen Anda untuk posisi {job} masuk ke tahap Onboarding.',
                'Silakan masuk ke portal kandidat untuk melihat informasi administrasi, jadwal, dan dokumen yang perlu dilengkapi.',
                'Tim kami akan menghubungi Anda apabila terdapat informasi tambahan.',
                'Hormat kami,',
                'Tim Rekrutmen PT Roda Jaya Sakti',
            ]),
            RecruitmentStage::HIRED => self::body([
                'Yth. {name},',
                'Selamat, Anda dinyatakan diterima untuk posisi {job} di PT Roda Jaya Sakti.',
                'Silakan masuk ke portal kandidat untuk melihat informasi lanjutan terkait onboarding dan kelengkapan administrasi.',
                'Kami menantikan kontribusi Anda bersama PT Roda Jaya Sakti.',
                'Hormat kami,',
                'Tim Rekrutmen PT Roda Jaya Sakti',
            ]),
            RecruitmentStage::REJECTED => self::body([
                'Yth. {name},',
                'Terima kasih telah mengikuti proses seleksi untuk posisi {job} di PT Roda Jaya Sakti.',
                'Setelah melalui proses pertimbangan, saat ini kami belum dapat melanjutkan lamaran Anda ke tahap berikutnya.',
                'Kami menghargai waktu dan minat Anda, serta berharap dapat bertemu kembali pada kesempatan lain yang sesuai.',
                'Hormat kami,',
                'Tim Rekrutmen PT Roda Jaya Sakti',
            ]),
            default => self::body([
                'Yth. {name},',
                'Kami informasikan bahwa status lamaran Anda untuk posisi {job} telah diperbarui menjadi {status}.',
                'Silakan masuk ke portal kandidat untuk melihat detail terbaru dan menindaklanjuti instruksi yang tersedia, apabila diperlukan.',
                'Hormat kami,',
                'Tim Rekrutmen PT Roda Jaya Sakti',
            ]),
        };
    }

    /**
     * @param  array<int, string>  $paragraphs
     */
    private static function body(array $paragraphs): string
    {
        return implode("\n\n", $paragraphs);
    }
}

<x-mail::message>
# {{ $roleType === 'candidate' ? 'Undangan Interview' : 'Jadwal Interview Kandidat' }}: {{ $interview->application->job->title }}

Yth. {{ $roleType === 'candidate' ? $interview->application->candidate->name : $interview->interviewer->name }},

@if($roleType === 'candidate')
Kami informasikan bahwa Anda dijadwalkan mengikuti interview untuk posisi **{{ $interview->application->job->title }}**.
@else
Kami informasikan bahwa Anda dijadwalkan sebagai interviewer untuk kandidat **{{ $interview->application->candidate->name }}** pada posisi **{{ $interview->application->job->title }}**.
@endif

Berikut detail jadwal yang telah ditetapkan:

<x-mail::panel>
**Tanggal:** {{ $interview->scheduled_at->format('d F Y') }}<br>
**Waktu:** {{ $interview->scheduled_at->format('H:i') }}<br>
**Interviewer:** {{ $interview->interviewer->name }}<br>
@if($interview->meeting_link)
**Tautan Meeting:** [Klik di sini untuk bergabung]({{ $interview->meeting_link }})
@else
**Lokasi/Tautan:** *Akan diinformasikan lebih lanjut.*
@endif
</x-mail::panel>

Mohon hadir sesuai jadwal dan mempersiapkan hal-hal yang diperlukan sebelum interview dimulai.

Terima kasih,<br>
Tim Rekrutmen PT Roda Jaya Sakti
</x-mail::message>

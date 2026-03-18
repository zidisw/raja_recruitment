<x-mail::message>
# Undangan Wawancara: {{ $interview->application->job->title }}

Halo {{ $roleType === 'candidate' ? $interview->application->candidate->name : $interview->interviewer->name }},

Anda memiliki jadwal wawancara untuk posisi **{{ $interview->application->job->title }}**.
Berikut adalah rincian jadwal yang telah diatur:

<x-mail::panel>
**Tanggal:** {{ $interview->scheduled_at->format('d F Y') }}<br>
**Waktu:** {{ $interview->scheduled_at->format('H:i') }} WIB<br>
**Interviewer:** {{ $interview->interviewer->name }}<br>
@if($interview->meeting_link)
**Meeting Link:** [Klik di sini untuk bergabung]({{ $interview->meeting_link }})
@else
**Lokasi/Link:** *Akan diinformasikan lebih lanjut.*
@endif
</x-mail::panel>

Harap bersiap 5 menit sebelum jadwal wawancara dimulai. 
Jika ada kendala terkait waktu wawancara, harap segera informasikan kepada kami.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>

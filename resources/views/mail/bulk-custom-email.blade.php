<x-mail::message>

{!! nl2br(e($body)) !!}

<x-mail::button :url="url(route('candidate.applications'))">
Lihat Lamaran Saya
</x-mail::button>

</x-mail::message>

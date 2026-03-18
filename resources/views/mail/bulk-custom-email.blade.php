<x-mail::message>

{!! nl2br(e($body)) !!}

<x-mail::button :url="url(route('candidate.applications'))">
{{ __('Track Your Application') }}
</x-mail::button>

</x-mail::message>

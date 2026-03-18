@props([
    'disabled' => false,
    'placeholder' => 'Select date...',
    'mode' => 'date', // date, time, datetime
    'format' => null,
])

@php
    $inputType = match ($mode) {
        'time' => 'time',
        'datetime' => 'datetime-local',
        default => 'date',
    };
@endphp

<flux:input
    :type="$inputType"
    :icon="$mode === 'time' ? 'clock' : 'calendar'"
    :disabled="$disabled"
    :placeholder="$placeholder"
    {{ $attributes }}
/>

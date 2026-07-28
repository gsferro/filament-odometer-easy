@php
    $locales ??= config('filament-odometer-easy.number-flow.locales');
    $format ??= config('filament-odometer-easy.number-flow.format');
    $delay ??= config('filament-odometer-easy.number-flow.delay', 500);
    $duration ??= config('filament-odometer-easy.number-flow.duration');
@endphp

<number-flow
    class="{{ \Illuminate\Support\Arr::toCssClasses(['fi-odometer-easy', $class ?? null]) }}"
    data-value="{{ $value ?? 0 }}"
    data-delay="{{ $delay }}"
    @if (filled($duration)) data-duration="{{ $duration }}" @endif
    @if (filled($locales)) data-locales="{{ is_string($locales) ? $locales : json_encode($locales) }}" @endif
    @if (filled($format)) data-format="{{ json_encode($format) }}" @endif
>0</number-flow>

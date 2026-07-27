@php
    $locales ??= config('filament-odometer-easy.number-flow.locales');
    $format ??= config('filament-odometer-easy.number-flow.format');
@endphp

<number-flow
    class="{{ \Illuminate\Support\Arr::toCssClasses(['fi-odometer-easy', $class ?? null]) }}"
    x-data
    x-init="
        @if (filled($locales)) $el.locales = @js($locales); @endif
        @if (filled($format)) $el.format = @js($format); @endif
        $nextTick(() => $el.update(@js($value ?? 0)))
    "
>{{ $value ?? 0 }}</number-flow>

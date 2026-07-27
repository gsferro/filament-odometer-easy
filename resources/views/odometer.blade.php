<span
    class="{{ \Illuminate\Support\Arr::toCssClasses(['odometer-item', 'odometer-auto-theme', $class ?? null]) }}"
    data-value="{{ $value ?? 0 }}"
    @if (filled($format ?? null)) data-format="{{ $format }}" @endif
>0</span>

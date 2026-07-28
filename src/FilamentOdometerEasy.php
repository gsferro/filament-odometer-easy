<?php

namespace Gsferro\FilamentOdometerEasy;

use Illuminate\Support\HtmlString;

class FilamentOdometerEasy
{
    /**
     * Renderiza o contador animado usando o driver configurado
     * (number-flow por padrão; odometer como alternativa).
     *
     * @param  string|array<string, mixed>|null  $format  string (data-format do odometer)
     *                                                    ou array (opções Intl.NumberFormat do number-flow)
     */
    public function render(mixed $value, string | array | null $format = null, ?string $class = null, ?string $driver = null, ?int $duration = null): HtmlString
    {
        $driver ??= config('filament-odometer-easy.driver', 'number-flow');

        return $driver === 'odometer'
            ? $this->renderOdometer($value, is_string($format) ? $format : null, $class)
            : $this->renderNumberFlow($value, is_array($format) ? $format : null, $class, duration: $duration);
    }

    /**
     * <number-flow> (web component, sem dependências): no primeiro render
     * exibe 0 e anima até o valor após o delay configurado; depois re-anima
     * a cada atualização de valor (Livewire poll/morph).
     *
     * @param  array<string, mixed>|null  $format  opções do Intl.NumberFormat
     */
    public function renderNumberFlow(mixed $value, ?array $format = null, ?string $class = null, ?int $delay = null, ?int $duration = null): HtmlString
    {
        return new HtmlString(
            view('filament-odometer-easy::number-flow', [
                'value' => is_numeric($value) ? $value + 0 : 0,
                'format' => $format ?? config('filament-odometer-easy.number-flow.format'),
                'locales' => config('filament-odometer-easy.number-flow.locales'),
                'delay' => $delay ?? config('filament-odometer-easy.number-flow.delay', 500),
                'duration' => $duration ?? config('filament-odometer-easy.number-flow.duration'),
                'class' => $class,
            ])->render()
        );
    }

    /**
     * <span> do odometer-easy, animado pelo odometer-easy.js
     * (todo elemento .odometer-item é inicializado pelo script).
     */
    public function renderOdometer(mixed $value, ?string $format = null, ?string $class = null): HtmlString
    {
        return new HtmlString(
            view('filament-odometer-easy::odometer', [
                'value' => $value ?? 0,
                'format' => $format ?? config('filament-odometer-easy.odometer.format'),
                'class' => $class,
            ])->render()
        );
    }
}

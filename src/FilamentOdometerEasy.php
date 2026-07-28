<?php

namespace Gsferro\FilamentOdometerEasy;

use Illuminate\Support\HtmlString;

class FilamentOdometerEasy
{
    /**
     * Marcador invisível (U+2060, WORD JOINER) que identifica valores de
     * badge de navegação a serem animados pelo JS do driver number-flow.
     */
    public const NAVIGATION_BADGE_MARKER = "\u{2060}";

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
     * Valor para getNavigationBadge()/NavigationItem::badge(): a API do
     * Filament só aceita string (HTML é escapado), então o valor é envolvido
     * pelo marcador invisível e o JS do driver number-flow troca o texto do
     * badge por um <number-flow> animado, com a formatação da config global.
     *
     * No driver odometer (sem suporte a badge), retorna o valor puro.
     */
    public function renderNavigationBadge(mixed $value): string
    {
        $value = is_numeric($value) ? $value + 0 : 0;

        if (config('filament-odometer-easy.driver', 'number-flow') !== 'number-flow') {
            return (string) $value;
        }

        return self::NAVIGATION_BADGE_MARKER . $value . self::NAVIGATION_BADGE_MARKER;
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

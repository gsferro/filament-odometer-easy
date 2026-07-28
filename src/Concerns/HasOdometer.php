<?php

namespace Gsferro\FilamentOdometerEasy\Concerns;

use Closure;
use Gsferro\FilamentOdometerEasy\FilamentOdometerEasy;
use Illuminate\Support\HtmlString;

trait HasOdometer
{
    protected string | array | Closure | null $odometerFormat = null;

    protected int | Closure | null $odometerDuration = null;

    /**
     * Formato do contador, de acordo com o driver:
     * - number-flow: array com opções do Intl.NumberFormat,
     *   ex.: ['style' => 'currency', 'currency' => 'BRL']
     * - odometer: string data-format, ex.: '(.ddd),dd'
     *
     * Quando não informado, usa o padrão do driver na config.
     *
     * @param  string|array<string, mixed>|Closure|null  $format
     */
    public function format(string | array | Closure | null $format): static
    {
        $this->odometerFormat = $format;

        return $this;
    }

    /**
     * @return string|array<string, mixed>|null
     */
    public function getOdometerFormat(): string | array | null
    {
        return $this->evaluateOdometerOption($this->odometerFormat);
    }

    /**
     * Velocidade da animação em ms (quanto maior, mais lenta) — driver
     * number-flow. Quando não informado, usa a config; null usa os timings
     * padrão do number-flow (~900ms).
     */
    public function duration(int | Closure | null $duration): static
    {
        $this->odometerDuration = $duration;

        return $this;
    }

    public function getOdometerDuration(): ?int
    {
        return $this->evaluateOdometerOption($this->odometerDuration);
    }

    /**
     * O Stat do Filament v3 não tem EvaluatesClosures (evaluate() só existe
     * nele a partir do v4): usa evaluate() quando disponível e resolve a
     * Closure diretamente como fallback.
     */
    protected function evaluateOdometerOption(mixed $option): mixed
    {
        if (method_exists($this, 'evaluate')) {
            return $this->evaluate($option);
        }

        return $option instanceof Closure ? $option() : $option;
    }

    protected function renderOdometer(mixed $value): HtmlString
    {
        return app(FilamentOdometerEasy::class)->render(
            $value,
            $this->getOdometerFormat(),
            duration: $this->getOdometerDuration(),
        );
    }
}

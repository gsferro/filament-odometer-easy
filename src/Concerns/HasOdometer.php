<?php

namespace Gsferro\FilamentOdometerEasy\Concerns;

use Closure;
use Gsferro\FilamentOdometerEasy\FilamentOdometerEasy;
use Illuminate\Support\HtmlString;

trait HasOdometer
{
    protected string | array | Closure | null $odometerFormat = null;

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
        return $this->evaluate($this->odometerFormat);
    }

    protected function renderOdometer(mixed $value): HtmlString
    {
        return app(FilamentOdometerEasy::class)->render($value, $this->getOdometerFormat());
    }
}

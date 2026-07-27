<?php

namespace Gsferro\FilamentOdometerEasy\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Gsferro\FilamentOdometerEasy\Concerns\HasOdometer;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Stat para o StatsOverviewWidget que renderiza o valor com o contador
 * animado do odometer-easy: OdometerStat::make('Total', $count).
 */
class OdometerStat extends Stat
{
    use HasOdometer;

    /**
     * @return scalar | Htmlable
     */
    public function getValue(): mixed
    {
        return $this->renderOdometer(parent::getValue());
    }
}

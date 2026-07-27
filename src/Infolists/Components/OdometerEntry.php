<?php

namespace Gsferro\FilamentOdometerEasy\Infolists\Components;

use Filament\Infolists\Components\TextEntry;
use Gsferro\FilamentOdometerEasy\Concerns\HasOdometer;
use Illuminate\Support\HtmlString;

/**
 * Entry de infolist que renderiza o valor com o contador animado do
 * odometer-easy. Herda todos os recursos do TextEntry:
 * OdometerEntry::make('field').
 */
class OdometerEntry extends TextEntry
{
    use HasOdometer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(
            fn (mixed $state): HtmlString => $this->renderOdometer($state)
        );
    }
}

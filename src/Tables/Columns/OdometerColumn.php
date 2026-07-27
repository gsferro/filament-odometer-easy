<?php

namespace Gsferro\FilamentOdometerEasy\Tables\Columns;

use Filament\Tables\Columns\TextColumn;
use Gsferro\FilamentOdometerEasy\Concerns\HasOdometer;
use Illuminate\Support\HtmlString;

/**
 * Coluna de tabela que renderiza o valor com o contador animado do
 * odometer-easy. Herda todos os recursos do TextColumn (sortable,
 * searchable, alignment etc.): OdometerColumn::make('field').
 */
class OdometerColumn extends TextColumn
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

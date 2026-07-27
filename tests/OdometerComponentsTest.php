<?php

use Filament\Schemas\Schema;
use Gsferro\FilamentOdometerEasy\Facades\FilamentOdometerEasy;
use Gsferro\FilamentOdometerEasy\Infolists\Components\OdometerEntry;
use Gsferro\FilamentOdometerEasy\Tables\Columns\OdometerColumn;
use Gsferro\FilamentOdometerEasy\Widgets\OdometerStat;
use Illuminate\Support\HtmlString;

/*
|--------------------------------------------------------------------------
| Driver number-flow (padrão)
|--------------------------------------------------------------------------
*/

it('uses number-flow as the default driver', function () {
    expect(config('filament-odometer-easy.driver'))->toBe('number-flow');
});

it('renders a number-flow element through the facade', function () {
    $html = FilamentOdometerEasy::render(1500);

    expect($html)
        ->toBeInstanceOf(HtmlString::class)
        ->and($html->toHtml())
        ->toContain('<number-flow')
        ->toContain('update(1500)')
        ->toContain('>1500</number-flow>');
});

it('passes locales and intl format options to number-flow', function () {
    config([
        'filament-odometer-easy.number-flow.locales' => 'pt-BR',
        'filament-odometer-easy.number-flow.format' => ['style' => 'currency', 'currency' => 'BRL'],
    ]);

    $html = FilamentOdometerEasy::render(99.9)->toHtml();

    expect($html)
        ->toContain("locales = 'pt-BR'")
        ->toContain('currency')
        ->toContain('BRL');
});

it('normalizes non numeric values to zero on number-flow', function () {
    expect(FilamentOdometerEasy::render(null)->toHtml())->toContain('update(0)');
});

/*
|--------------------------------------------------------------------------
| Driver odometer (secundário)
|--------------------------------------------------------------------------
*/

it('renders the odometer driver when configured', function () {
    config(['filament-odometer-easy.driver' => 'odometer']);

    $html = FilamentOdometerEasy::render(1500)->toHtml();

    expect($html)
        ->toContain('data-value="1500"')
        ->toContain('odometer-item')
        ->toContain('odometer-auto-theme')
        ->not->toContain('data-format');
});

it('renders the odometer view with a custom format and class', function () {
    $html = view('filament-odometer-easy::odometer', [
        'value' => 99,
        'format' => '(.ddd),dd',
        'class' => 'h3',
    ])->render();

    expect($html)
        ->toContain('data-value="99"')
        ->toContain('data-format="(.ddd),dd"')
        ->toContain('h3');
});

it('uses the configured odometer default format when none is given', function () {
    config([
        'filament-odometer-easy.driver' => 'odometer',
        'filament-odometer-easy.odometer.format' => '(,ddd)',
    ]);

    expect(FilamentOdometerEasy::render(10)->toHtml())
        ->toContain('data-format="(,ddd)"');
});

/*
|--------------------------------------------------------------------------
| Componentes Filament
|--------------------------------------------------------------------------
*/

it('formats the state of an odometer column with the default driver', function () {
    $formatted = OdometerColumn::make('total')->formatState(1234);

    expect($formatted)
        ->toBeInstanceOf(HtmlString::class)
        ->and($formatted->toHtml())
        ->toContain('<number-flow')
        ->toContain('update(1234)');
});

it('passes intl format options from the column to number-flow', function () {
    $formatted = OdometerColumn::make('total')
        ->format(['style' => 'currency', 'currency' => 'BRL'])
        ->formatState(7);

    expect($formatted->toHtml())->toContain('currency');
});

it('formats the state of an odometer column with the odometer driver', function () {
    config(['filament-odometer-easy.driver' => 'odometer']);

    $formatted = OdometerColumn::make('total')->format('d')->formatState(7);

    expect($formatted->toHtml())
        ->toContain('data-value="7"')
        ->toContain('data-format="d"');
});

it('formats the state of an odometer entry with the default driver', function () {
    $formatted = OdometerEntry::make('total')
        ->container(Schema::make())
        ->formatState(555);

    expect((string) $formatted)
        ->toContain('<number-flow')
        ->toContain('update(555)');
});

it('formats the state of an odometer entry with the odometer driver', function () {
    config(['filament-odometer-easy.driver' => 'odometer']);

    $formatted = OdometerEntry::make('total')
        ->container(Schema::make())
        ->format('(,ddd)')
        ->formatState(555);

    expect((string) $formatted)
        ->toContain('data-value="555"')
        ->toContain('data-format="(,ddd)"');
});

it('renders the stat value with the default driver', function () {
    $value = OdometerStat::make('Total de vendas', 9876)->getValue();

    expect($value)
        ->toBeInstanceOf(HtmlString::class)
        ->and($value->toHtml())
        ->toContain('<number-flow')
        ->toContain('update(9876)');
});

it('renders the stat value with the odometer driver', function () {
    config(['filament-odometer-easy.driver' => 'odometer']);

    $value = OdometerStat::make('Total', 10)->format('(,ddd).dd')->getValue();

    expect($value->toHtml())
        ->toContain('data-value="10"')
        ->toContain('data-format="(,ddd).dd"');
});

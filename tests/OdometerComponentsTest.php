<?php

use Filament\Schemas\Schema;
use Gsferro\FilamentOdometerEasy\Facades\FilamentOdometerEasy;
use Gsferro\FilamentOdometerEasy\Infolists\Components\OdometerEntry;
use Gsferro\FilamentOdometerEasy\Navigation\OdometerNavigationBadge;
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
        ->toContain('data-value="1500"')
        ->toContain('data-delay="500"')
        ->toContain('>0</number-flow>');
});

it('passes locales and intl format options to number-flow', function () {
    config([
        'filament-odometer-easy.number-flow.locales' => 'pt-BR',
        'filament-odometer-easy.number-flow.format' => ['style' => 'currency', 'currency' => 'BRL'],
    ]);

    $html = FilamentOdometerEasy::render(99.9)->toHtml();

    expect($html)
        ->toContain('data-locales="pt-BR"')
        ->toContain('currency')
        ->toContain('BRL');
});

it('applies a custom initial animation delay', function () {
    config(['filament-odometer-easy.number-flow.delay' => 1000]);

    expect(FilamentOdometerEasy::render(10)->toHtml())
        ->toContain('data-delay="1000"');

    expect(FilamentOdometerEasy::renderNumberFlow(10, delay: 50)->toHtml())
        ->toContain('data-delay="50"');
});

it('normalizes non numeric values to zero on number-flow', function () {
    expect(FilamentOdometerEasy::render(null)->toHtml())->toContain('data-value="0"');
});

it('controls the animation speed through the duration option', function () {
    expect(FilamentOdometerEasy::render(10)->toHtml())
        ->not->toContain('data-duration');

    config(['filament-odometer-easy.number-flow.duration' => 2000]);

    expect(FilamentOdometerEasy::render(10)->toHtml())
        ->toContain('data-duration="2000"');

    expect(OdometerColumn::make('total')->duration(750)->formatState(7)->toHtml())
        ->toContain('data-duration="750"');
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
        ->toContain('data-value="1234"');
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
        ->toContain('data-value="555"');
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
        ->toContain('data-value="9876"');
});

it('wraps the navigation badge value with invisible markers on number-flow', function () {
    $marker = \Gsferro\FilamentOdometerEasy\FilamentOdometerEasy::NAVIGATION_BADGE_MARKER;

    expect($marker)->toBe("\u{2060}")
        ->and(OdometerNavigationBadge::make(1234))->toBe("{$marker}1234{$marker}")
        ->and(OdometerNavigationBadge::make('99.9'))->toBe("{$marker}99.9{$marker}");
});

it('normalizes non numeric navigation badge values to zero', function () {
    $marker = \Gsferro\FilamentOdometerEasy\FilamentOdometerEasy::NAVIGATION_BADGE_MARKER;

    expect(OdometerNavigationBadge::make(null))->toBe("{$marker}0{$marker}");
});

it('returns a plain navigation badge value on the odometer driver', function () {
    config(['filament-odometer-easy.driver' => 'odometer']);

    expect(OdometerNavigationBadge::make(1234))->toBe('1234');
});

it('renders the stat value with the odometer driver', function () {
    config(['filament-odometer-easy.driver' => 'odometer']);

    $value = OdometerStat::make('Total', 10)->format('(,ddd).dd')->getValue();

    expect($value->toHtml())
        ->toContain('data-value="10"')
        ->toContain('data-format="(,ddd).dd"');
});

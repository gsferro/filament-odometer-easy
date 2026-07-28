<p align="center">
    <img src="/art/logo.png" alt="filament-odometer-easy">
</p>

<p align="center">
    <a href="https://packagist.org/packages/gsferro/filament-odometer-easy">
        <img alt="Latest Version" src="https://img.shields.io/packagist/v/gsferro/filament-odometer-easy">
    </a>
    <a href="https://packagist.org/packages/gsferro/filament-odometer-easy">
        <img alt="Total Downloads" src="https://img.shields.io/packagist/dt/gsferro/filament-odometer-easy">
    </a>
    <a href="https://packagist.org/packages/gsferro/filament-odometer-easy">
        <img alt="License" src="https://img.shields.io/packagist/l/gsferro/filament-odometer-easy">
    </a>
</p>

# Filament Odometer Easy

> 🇺🇸 English · 🇧🇷 [Português](README.md)

Animated counters for **Filament v3, v4 and v5** — tables, infolists and stats widgets — the easiest way possible: install, register the plugin and use it.

It's the same effect as the **"Items found"** counter on the official [filamentphp.com/plugins](https://filamentphp.com/plugins) page, ready for your dashboards and real-time metrics.

## 🎬 Demo

**`OdometerStat` on the dashboard** — with `poll`, the counters re-animate on their own every time the value updates:

![OdometerStat with polling](/art/OdometerStat.gif)

**`OdometerColumn` in tables** — animates on load, on sorting and on page change:

![OdometerColumn in a table](/art/OdometerColumn.gif)

**`OdometerEntry` in infolists and `OdometerNavigationBadge` in menus**:

![OdometerEntry in an infolist and navigation badge](/art/odometer.gif)

## Components

| Component | Extends | Usage |
|---|---|---|
| `OdometerColumn` | `TextColumn` | Table columns |
| `OdometerEntry` | `TextEntry` | Infolist entries |
| `OdometerStat` | `Stat` | Counts in `StatsOverviewWidget` |
| `OdometerNavigationBadge` | — | Navigation badge (`getNavigationBadge()`) |
| `FilamentOdometerEasy` facade | — | Any custom view/blade |

All of them inherit **100% of the base component API** (`sortable`, `searchable`, `label`, `description`, `color` etc.) — only the value becomes animated.

## Animation engines (drivers)

The package ships two engines and you pick one via config or fluently on the plugin:

### `number-flow` — default ⭐

The [number-flow](https://github.com/barvian/number-flow) web component (used by the Filament website itself):

- ✅ **Zero dependencies** — no jQuery, no CDN; the bundle (~16 KB) ships with the package
- ✅ **Animates from 0 on first render** — shows 0 and, after a configurable delay, animates up to the value
- ✅ **Re-animates on every update** — perfect with Livewire, `poll()` and real-time dashboards
- ✅ **Native formatting via `Intl.NumberFormat`** — currency, decimals and locale (`pt-BR` → `1.000,00`)
- ✅ **Accessible** — respects `prefers-reduced-motion`
- ✅ Actively maintained

### `odometer` — secondary

The classic [odometer.js](https://github.com/HubSpot/odometer) effect via [gsferro/laravel-odometer-easy](https://github.com/gsferro/laravel-odometer-easy) (installed as a dependency):

- 🎨 7 visual themes: `default`, `car`, `digital`, `minimal`, `plaza`, `slot-machine`, `train-station`
- ⚠️ Depends on jQuery (the plugin injects it automatically into the panels' `<head>`)
- ⚠️ Animates only on first render (does not re-animate when the value updates)

## Compatibility

| Filament | Support | Notes |
|---|---|---|
| **5.x** | ✅ | |
| **4.x** | ✅ | |
| **3.x** (3.2+) | ✅ | |

The same package version covers all three — Composer resolves it against your project's Filament version. Requires PHP 8.2+.

## Installation

```bash
composer require gsferro/filament-odometer-easy
php artisan filament:assets
```

Register the plugin in your panel:

```php
use Gsferro\FilamentOdometerEasy\FilamentOdometerEasyPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(FilamentOdometerEasyPlugin::make());
}
```

Done. ✨ No npm, no publishing views, no asset configuration — the `number-flow` driver just works.

> [!TIP]
> Most apps already run `filament:assets` automatically on `post-autoload-dump`
> (via `filament:upgrade`). In that case, `composer require` is all you need.

## Usage

### Table column

```php
use Gsferro\FilamentOdometerEasy\Tables\Columns\OdometerColumn;

OdometerColumn::make('total_sales')
    ->label('Total sales')
    ->sortable(),
```

### Infolist entry

```php
use Gsferro\FilamentOdometerEasy\Infolists\Components\OdometerEntry;

OdometerEntry::make('total_sales')
    ->label('Total sales'),
```

### Stat (StatsOverviewWidget)

```php
use Gsferro\FilamentOdometerEasy\Widgets\OdometerStat;

protected function getStats(): array
{
    return [
        OdometerStat::make('Total sales', Sale::count())
            ->description('Last 30 days')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success'),
    ];
}
```

> [!TIP]
> Combine it with `->poll('10s')` on the widget: with the `number-flow` driver,
> the counter re-animates on every value update. 📈

### Navigation badge (panel menu)

```php
use Gsferro\FilamentOdometerEasy\Navigation\OdometerNavigationBadge;

// on a Resource (or Page)
public static function getNavigationBadge(): ?string
{
    return OdometerNavigationBadge::make(static::getModel()::count());
}

// or on a custom NavigationItem
NavigationItem::make('Sales')
    ->badge(fn (): string => OdometerNavigationBadge::make(Sale::count())),
```

Filament's navigation API only accepts `string` (HTML is escaped), so the
component wraps the value with an invisible marker and the package's JS swaps
the badge text for an animated `<number-flow>`. Formatting uses the global
`number-flow` config (`locales`, `format`, `delay`, `duration`).

> [!NOTE]
> Available only on the `number-flow` driver. On the `odometer` driver,
> the value is displayed as plain text, without animation.

### In any view (facade)

```php
use Gsferro\FilamentOdometerEasy\Facades\FilamentOdometerEasy;

// configured driver (number-flow by default)
FilamentOdometerEasy::render(1500);

// forcing a driver on the spot
FilamentOdometerEasy::renderNumberFlow(1500, format: ['style' => 'currency', 'currency' => 'BRL']);
FilamentOdometerEasy::renderOdometer(1500, format: '(.ddd),dd', class: 'h3');
```

## Formatting

The `->format()` method is available on every component and accepts the active driver's format:

```php
// number-flow: array of Intl.NumberFormat options
OdometerColumn::make('revenue')
    ->format(['style' => 'currency', 'currency' => 'BRL']),

OdometerStat::make('Conversion', $rate)
    ->format(['style' => 'percent', 'minimumFractionDigits' => 1]),

// odometer: data-format string
OdometerColumn::make('revenue')
    ->format('(.ddd),dd'),
```

### Animation speed

Every component accepts `->duration()` (number-flow driver; the higher, the slower):

```php
OdometerStat::make('Revenue', $total)
    ->duration(2000), // counts in slow motion ✨
```

References: [Intl.NumberFormat options](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat/NumberFormat#options) · [odometer format](https://github.com/HubSpot/odometer#api).

## Configuration

### Fluently, right on the plugin

```php
FilamentOdometerEasyPlugin::make()
    ->locales('pt-BR')                                      // number-flow: 1.000,00
    ->format(['style' => 'currency', 'currency' => 'BRL'])  // global default
    ->delay(500)                                            // ms before the initial animation (0 → value)
    ->duration(1500),                                       // animation speed in ms (default ~900ms)
```

To use the classic engine:

```php
FilamentOdometerEasyPlugin::make()
    ->driver('odometer')
    ->theme('digital')          // default, car, digital, minimal, plaza, slot-machine, train-station
    ->format('(.ddd),dd')       // default data-format
    ->jquery(enabled: false),   // when your application already loads jQuery
```

### Or through the config file

```bash
php artisan vendor:publish --tag="filament-odometer-easy-config"
```

```php
return [
    // number-flow (default) | odometer
    'driver' => 'number-flow',

    'number-flow' => [
        'locales' => null,  // e.g. 'pt-BR'; null uses the browser locale
        'format' => null,   // e.g. ['style' => 'currency', 'currency' => 'BRL']
        'delay' => 500,     // ms before the initial animation: shows 0 and animates up to the value
        'duration' => null, // animation speed in ms; null uses the default (~900ms)
    ],

    'odometer' => [
        'theme' => 'default',
        'format' => null,  // e.g. '(.ddd),dd'; null uses the default (pt-BR: 1.000,00)

        'jquery' => [
            'enabled' => true,
            'src' => 'https://code.jquery.com/jquery-4.0.0.min.js',
            'integrity' => 'sha256-OaVG6prZf4v69dPg6PhVattBXkcOWQB62pdZ3ORyrao=',
        ],
    ],
];
```

## How it works under the hood

- **number-flow**: the package ships the `<number-flow>` web component bundled
  (`resources/dist/filament-odometer-easy.js`, registered as an ES module via `FilamentAsset`),
  the same one used on filamentphp.com/plugins. The Blade view renders the element with
  `data-value`/`data-format`/`data-locales` and the bundle initializes it: shows 0, waits for the
  `delay` and animates up to the value. A `MutationObserver` tracks `data-value` changes
  made by Livewire's morph (poll, refresh) and re-animates from the current value to the new one —
  without relying on `x-init`, which does not run again when Livewire preserves the element.
- **Navigation badge**: `getNavigationBadge()` and `NavigationItem::badge()` are typed
  as `?string` and Blade escapes the content, so returning HTML is not possible.
  `OdometerNavigationBadge::make()` wraps the value with `U+2060` (word joiner, invisible);
  the bundle detects the marker inside `.fi-badge-label`, swaps the text for a `<number-flow>`
  and uses the global config exposed on `window.filamentOdometerEasy` via render hook.
  When Livewire re-renders the badge, the animation starts from the previous value (`data-start`).
- **odometer**: the assets (css theme, `odometer.js`, `odometer-easy.js`) are served
  straight from the `gsferro/odometer-easy` vendor via `FilamentAsset`, and jQuery is injected
  via render hook into the panels' `<head>`.
- Switching drivers selects which assets get registered — never both at the same time.

## Development

The number-flow bundle only needs to be rebuilt if you change `resources/js/index.js`:

```bash
npm install
npm run build
```

## Tests

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [gsferro](https://github.com/gsferro)
- [number-flow](https://github.com/barvian/number-flow) by Maxwell Barvian
- [odometer.js](https://github.com/HubSpot/odometer) by HubSpot
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

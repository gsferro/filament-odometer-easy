<p align="center">
    <img src="logo.png" alt="filament-odometer-easy">
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

Contadores animados para o **Filament v5** — tabelas, infolists e widgets de estatísticas — do jeito mais simples possível: instale, registre o plugin e use.

É o mesmo efeito do contador **"Items found"** da página oficial [filamentphp.com/plugins](https://filamentphp.com/plugins), pronto para os seus dashboards e métricas em tempo real.

## Componentes

| Componente | Estende | Uso |
|---|---|---|
| `OdometerColumn` | `TextColumn` | Colunas de tabela |
| `OdometerEntry` | `TextEntry` | Entries de infolist |
| `OdometerStat` | `Stat` | Counts no `StatsOverviewWidget` |
| Facade `FilamentOdometerEasy` | — | Qualquer view/blade customizado |

Todos herdam **100% da API do componente base** (`sortable`, `searchable`, `label`, `description`, `color` etc.) — só o valor passa a ser animado.

## Motores de animação (drivers)

O pacote traz dois motores e você escolhe por config ou de forma fluente no plugin:

### `number-flow` — padrão ⭐

O web component [number-flow](https://github.com/barvian/number-flow) (usado pelo próprio site do Filament):

- ✅ **Zero dependências** — sem jQuery, sem CDN; o bundle (~16 KB) já vem no pacote
- ✅ **Re-anima a cada atualização** — perfeito com Livewire, `poll()` e dashboards em tempo real
- ✅ **Formatação nativa via `Intl.NumberFormat`** — moeda, decimais e locale (`pt-BR` → `1.000,00`)
- ✅ **Acessível** — respeita `prefers-reduced-motion`
- ✅ Mantido ativamente

### `odometer` — secundário

O efeito clássico do [odometer.js](https://github.com/HubSpot/odometer) via [gsferro/laravel-odometer-easy](https://github.com/gsferro/laravel-odometer-easy) (instalado como dependência):

- 🎨 7 temas visuais: `default`, `car`, `digital`, `minimal`, `plaza`, `slot-machine`, `train-station`
- ⚠️ Depende do jQuery (o plugin injeta automaticamente no `<head>` dos painéis)
- ⚠️ Anima apenas na primeira renderização (não re-anima ao atualizar o valor)

## Instalação

```bash
composer require gsferro/filament-odometer-easy
php artisan filament:assets
```

Registre o plugin no seu painel:

```php
use Gsferro\FilamentOdometerEasy\FilamentOdometerEasyPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(FilamentOdometerEasyPlugin::make());
}
```

Pronto. ✨ Sem npm, sem publicar views, sem configurar assets — o driver `number-flow` já funciona.

> [!TIP]
> A maioria das apps já roda `filament:assets` automaticamente no `post-autoload-dump`
> (via `filament:upgrade`). Nesse caso, basta o `composer require`.

## Uso

### Coluna de tabela

```php
use Gsferro\FilamentOdometerEasy\Tables\Columns\OdometerColumn;

OdometerColumn::make('total_vendas')
    ->label('Total de vendas')
    ->sortable(),
```

### Entry de infolist

```php
use Gsferro\FilamentOdometerEasy\Infolists\Components\OdometerEntry;

OdometerEntry::make('total_vendas')
    ->label('Total de vendas'),
```

### Stat (StatsOverviewWidget)

```php
use Gsferro\FilamentOdometerEasy\Widgets\OdometerStat;

protected function getStats(): array
{
    return [
        OdometerStat::make('Total de vendas', Venda::count())
            ->description('Últimos 30 dias')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success'),
    ];
}
```

> [!TIP]
> Combine com `->poll('10s')` no widget: com o driver `number-flow`,
> o contador re-anima a cada atualização de valor. 📈

### Em qualquer view (facade)

```php
use Gsferro\FilamentOdometerEasy\Facades\FilamentOdometerEasy;

// driver configurado (number-flow por padrão)
FilamentOdometerEasy::render(1500);

// forçando um driver pontualmente
FilamentOdometerEasy::renderNumberFlow(1500, format: ['style' => 'currency', 'currency' => 'BRL']);
FilamentOdometerEasy::renderOdometer(1500, format: '(.ddd),dd', class: 'h3');
```

## Formatação

O método `->format()` está disponível em todos os componentes e aceita o formato do driver ativo:

```php
// number-flow: array com opções do Intl.NumberFormat
OdometerColumn::make('receita')
    ->format(['style' => 'currency', 'currency' => 'BRL']),

OdometerStat::make('Conversão', $taxa)
    ->format(['style' => 'percent', 'minimumFractionDigits' => 1]),

// odometer: string data-format
OdometerColumn::make('receita')
    ->format('(.ddd),dd'),
```

Referências: [opções do Intl.NumberFormat](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat/NumberFormat#options) · [format do odometer](https://github.com/HubSpot/odometer#api).

## Configuração

### Fluente, direto no plugin

```php
FilamentOdometerEasyPlugin::make()
    ->locales('pt-BR')                                      // number-flow: 1.000,00
    ->format(['style' => 'currency', 'currency' => 'BRL']), // padrão global
```

Para usar o motor clássico:

```php
FilamentOdometerEasyPlugin::make()
    ->driver('odometer')
    ->theme('digital')          // default, car, digital, minimal, plaza, slot-machine, train-station
    ->format('(.ddd),dd')       // data-format padrão
    ->jquery(enabled: false),   // quando a aplicação já carrega o jQuery
```

### Ou pelo arquivo de config

```bash
php artisan vendor:publish --tag="filament-odometer-easy-config"
```

```php
return [
    // number-flow (padrão) | odometer
    'driver' => 'number-flow',

    'number-flow' => [
        'locales' => null, // ex.: 'pt-BR'; null usa o locale do navegador
        'format' => null,  // ex.: ['style' => 'currency', 'currency' => 'BRL']
    ],

    'odometer' => [
        'theme' => 'default',
        'format' => null,  // ex.: '(.ddd),dd'; null usa o padrão (pt-BR: 1.000,00)

        'jquery' => [
            'enabled' => true,
            'src' => 'https://code.jquery.com/jquery-4.0.0.min.js',
            'integrity' => 'sha256-OaVG6prZf4v69dPg6PhVattBXkcOWQB62pdZ3ORyrao=',
        ],
    ],
];
```

## Como funciona por baixo dos panos

- **number-flow**: o pacote já entrega o web component `<number-flow>` bundlado
  (`resources/dist/filament-odometer-easy.js`, registrado como ES module via `FilamentAsset`).
  A view Blade renderiza o elemento e o Alpine (que o Filament já carrega) dispara
  `$el.update(valor)` — exatamente a técnica usada em filamentphp.com/plugins.
- **odometer**: os assets (tema css, `odometer.js`, `odometer-easy.js`) são servidos
  direto do vendor do `gsferro/odometer-easy` via `FilamentAsset`, e o jQuery é injetado
  por render hook no `<head>` dos painéis.
- A troca de driver seleciona quais assets são registrados — nunca os dois ao mesmo tempo.

## Desenvolvimento

O bundle do number-flow só precisa ser regerado se você alterar `resources/js/index.js`:

```bash
npm install
npm run build
```

## Testes

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

<p align="center">
    <img src="https://raw.githubusercontent.com/gsferro/filament-odometer-easy/main/art/logo-light.png" alt="filament-odometer-easy">
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

> 🇧🇷 Português · 🇺🇸 [English](https://github.com/gsferro/filament-odometer-easy/blob/main/README.en.md)

Contadores animados para o **Filament v3, v4 e v5** — tabelas, infolists e widgets de estatísticas — do jeito mais simples possível: instale, registre o plugin e use.

É o mesmo efeito do contador **"Items found"** da página oficial [filamentphp.com/plugins](https://filamentphp.com/plugins), pronto para os seus dashboards e métricas em tempo real.

## 🎬 Demo

**`OdometerStat` no dashboard** — com `poll`, os contadores re-animam sozinhos a cada atualização de valor:

![OdometerStat com polling](https://raw.githubusercontent.com/gsferro/filament-odometer-easy/main/art/OdometerStat.gif)

**`OdometerColumn` em tabelas** — animação no load, na ordenação e na troca de página:

![OdometerColumn em tabela](https://raw.githubusercontent.com/gsferro/filament-odometer-easy/main/art/OdometerColumn.gif)

**`OdometerEntry` em infolists e `OdometerNavigationBadge` em menus**:

![OdometerEntry em infolist e navigation badge](https://raw.githubusercontent.com/gsferro/filament-odometer-easy/main/art/odometer.gif)

## Componentes

| Componente | Estende | Uso |
|---|---|---|
| `OdometerColumn` | `TextColumn` | Colunas de tabela |
| `OdometerEntry` | `TextEntry` | Entries de infolist |
| `OdometerStat` | `Stat` | Counts no `StatsOverviewWidget` |
| `OdometerNavigationBadge` | — | Badge de navegação (`getNavigationBadge()`) |
| Facade `FilamentOdometerEasy` | — | Qualquer view/blade customizado |

Todos herdam **100% da API do componente base** (`sortable`, `searchable`, `label`, `description`, `color` etc.) — só o valor passa a ser animado.

## Motores de animação (drivers)

O pacote traz dois motores e você escolhe por config ou de forma fluente no plugin:

### `number-flow` — padrão ⭐

O web component [number-flow](https://github.com/barvian/number-flow) (usado pelo próprio site do Filament):

- ✅ **Zero dependências** — sem jQuery, sem CDN; o bundle (~16 KB) já vem no pacote
- ✅ **Anima do 0 no primeiro render** — exibe 0 e, após um delay configurável, anima até o valor
- ✅ **Re-anima a cada atualização** — perfeito com Livewire, `poll()` e dashboards em tempo real
- ✅ **Formatação nativa via `Intl.NumberFormat`** — moeda, decimais e locale (`pt-BR` → `1.000,00`)
- ✅ **Acessível** — respeita `prefers-reduced-motion`
- ✅ Mantido ativamente

### `odometer` — secundário

O efeito clássico do [odometer.js](https://github.com/HubSpot/odometer) via [gsferro/laravel-odometer-easy](https://github.com/gsferro/laravel-odometer-easy) (instalado como dependência):

- 🎨 7 temas visuais: `default`, `car`, `digital`, `minimal`, `plaza`, `slot-machine`, `train-station`
- ⚠️ Depende do jQuery (o plugin injeta automaticamente no `<head>` dos painéis)
- ⚠️ Anima apenas na primeira renderização (não re-anima ao atualizar o valor)

## Compatibilidade

| Filament | Suporte | Observações |
|---|---|---|
| **5.x** | ✅ | |
| **4.x** | ✅ | |
| **3.x** (3.2+) | ✅ | |

A mesma versão do pacote atende as três — o Composer resolve pela versão do Filament do seu projeto. Requer PHP 8.2+.

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

### Badge de navegação (menu do painel)

```php
use Gsferro\FilamentOdometerEasy\Navigation\OdometerNavigationBadge;

// no Resource (ou Page)
public static function getNavigationBadge(): ?string
{
    return OdometerNavigationBadge::make(static::getModel()::count());
}

// ou em um NavigationItem customizado
NavigationItem::make('Vendas')
    ->badge(fn (): string => OdometerNavigationBadge::make(Venda::count())),
```

A API de navegação do Filament só aceita `string` (HTML é escapado), então o
componente envolve o valor com um marcador invisível e o JS do pacote troca o
texto do badge por um `<number-flow>` animado. A formatação usa a config global
do `number-flow` (`locales`, `format`, `delay`, `duration`).

> [!NOTE]
> Disponível apenas no driver `number-flow`. No driver `odometer`,
> o valor é exibido como texto puro, sem animação.

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

O método `->format()` está disponível em todos os componentes e aceita o formato do driver ativo. No driver `number-flow` (padrão), passe um array com [opções do Intl.NumberFormat](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat/NumberFormat#options) — a formatação (símbolo, separadores, casas decimais) é aplicada pelo navegador, animada dígito a dígito.

### Moeda (R$, US$, €…)

Por padrão o contador exibe apenas o número. Para mostrar o **símbolo da moeda**, passe um `format` com `style: currency`:

```php
OdometerStat::make('Valor aprovado (projetos em andamento)', $aprovado)
    ->format(['style' => 'currency', 'currency' => 'BRL']),
```

> [!TIP]
> Combine com `->locales('pt-BR')` no plugin (ou na config) para obter
> `R$ 1.234,56` — sem locale, o navegador do usuário decide os separadores.

### Receitas prontas (driver `number-flow`)

| Resultado (pt-BR) | `->format([...])` |
|---|---|
| `R$ 1.234,56` (moeda) | `['style' => 'currency', 'currency' => 'BRL']` |
| `R$ 1.235` (moeda sem centavos) | `['style' => 'currency', 'currency' => 'BRL', 'maximumFractionDigits' => 0]` |
| `US$ 1.234,56` / `€ 1.234,56` | `['style' => 'currency', 'currency' => 'USD']` / `'EUR'` |
| `12,5%` (percentual) | `['style' => 'percent', 'minimumFractionDigits' => 1]` |
| `1.234,50` (decimais fixos) | `['minimumFractionDigits' => 2, 'maximumFractionDigits' => 2]` |
| `1,2 mi` (notação compacta) | `['notation' => 'compact']` |
| `1.234 km` (unidades) | `['style' => 'unit', 'unit' => 'kilometer']` |
| `+1.234` (sinal sempre visível) | `['signDisplay' => 'always']` |
| `1234` (sem agrupamento) | `['useGrouping' => false]` |

> [!WARNING]
> `style: percent` multiplica o valor por 100 — passe `0.125` para exibir `12,5%`.

### Formato dinâmico (Closure)

`->format()` também aceita Closure. Em colunas e entries, o Filament injeta `$record`/`$state`:

```php
OdometerColumn::make('saldo')
    ->format(fn (Conta $record): array => [
        'style' => 'currency',
        'currency' => $record->moeda, // BRL, USD, EUR...
    ]),
```

### Velocidade da animação

Todos os componentes aceitam `->duration()` (driver number-flow; quanto maior, mais lento):

```php
OdometerStat::make('Receita', $total)
    ->duration(2000), // conta em câmera lenta ✨
```

### Driver `odometer`

No driver secundário, `->format()` recebe a string `data-format` do odometer.js:

```php
OdometerColumn::make('receita')
    ->format('(.ddd),dd'),
```

### Onde configurar cada opção do `number-flow`

| Opção | Por componente | Global (plugin/config) | O que faz |
|---|---|---|---|
| `format` | `->format([...])` | `->format([...])` | Opções do Intl.NumberFormat (moeda, percentual, decimais…) |
| `duration` | `->duration(ms)` | `->duration(ms)` | Velocidade da animação (padrão ~900ms) |
| `locales` | — | `->locales('pt-BR')` | Idioma/separadores (`1.000,00`) |
| `delay` | — | `->delay(ms)` | Espera antes da animação inicial 0 → valor (padrão 500ms) |

O valor por componente sempre vence o global. A facade `FilamentOdometerEasy::renderNumberFlow()` aceita todas as opções por chamada (`format`, `delay`, `duration`).

Referências: [opções do Intl.NumberFormat](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat/NumberFormat#options) · [format do odometer](https://github.com/HubSpot/odometer#api).

## Configuração

### Fluente, direto no plugin

```php
FilamentOdometerEasyPlugin::make()
    ->locales('pt-BR')                                      // number-flow: 1.000,00
    ->format(['style' => 'currency', 'currency' => 'BRL'])  // padrão global
    ->delay(500)                                            // ms antes da animação inicial (0 → valor)
    ->duration(1500),                                       // velocidade da animação em ms (padrão ~900ms)
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
        'locales' => null,  // ex.: 'pt-BR'; null usa o locale do navegador
        'format' => null,   // ex.: ['style' => 'currency', 'currency' => 'BRL']
        'delay' => 500,     // ms antes da animação inicial: exibe 0 e anima até o valor
        'duration' => null, // velocidade da animação em ms; null usa o padrão (~900ms)
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
  (`resources/dist/filament-odometer-easy.js`, registrado como ES module via `FilamentAsset`),
  o mesmo usado em filamentphp.com/plugins. A view Blade renderiza o elemento com
  `data-value`/`data-format`/`data-locales` e o bundle o inicializa: exibe 0, espera o
  `delay` e anima até o valor. Um `MutationObserver` acompanha as mudanças de `data-value`
  feitas pelo morph do Livewire (poll, refresh) e re-anima do valor atual para o novo —
  sem depender de `x-init`, que não roda de novo quando o Livewire preserva o elemento.
- **Navigation badge**: `getNavigationBadge()` e `NavigationItem::badge()` são tipados
  como `?string` e o Blade escapa o conteúdo, então não dá para retornar HTML.
  `OdometerNavigationBadge::make()` envolve o valor com `U+2060` (word joiner, invisível);
  o bundle detecta o marcador no `.fi-badge-label`, troca o texto por um `<number-flow>`
  e usa a config global exposta em `window.filamentOdometerEasy` por render hook.
  Quando o Livewire re-renderiza o badge, a animação parte do valor anterior (`data-start`).
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

Please see [CHANGELOG](https://github.com/gsferro/filament-odometer-easy/blob/main/CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/gsferro/filament-odometer-easy/blob/main/.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](https://github.com/gsferro/filament-odometer-easy/blob/main/.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [gsferro](https://github.com/gsferro)
- [number-flow](https://github.com/barvian/number-flow) by Maxwell Barvian
- [odometer.js](https://github.com/HubSpot/odometer) by HubSpot
- [All Contributors](https://github.com/gsferro/filament-odometer-easy/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](https://github.com/gsferro/filament-odometer-easy/blob/main/LICENSE.md) for more information.

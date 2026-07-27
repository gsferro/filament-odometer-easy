# Changelog

All notable changes to `filament-odometer-easy` will be documented in this file.

## v1.0 - 2026-07-27

Primeira release. 🎉

### Componentes

- `OdometerColumn` (tabelas), `OdometerEntry` (infolists) e `OdometerStat` (StatsOverviewWidget) com contadores animados, herdando 100% da API dos componentes base do Filament v5
- Facade `FilamentOdometerEasy` com `render()`, `renderNumberFlow()` e `renderOdometer()` para uso em qualquer view

### Drivers

- **number-flow** (padrão): web component sem dependências, bundlado no pacote (~16 KB), com formatação via `Intl.NumberFormat`, suporte a locale (`pt-BR`), acessibilidade (`prefers-reduced-motion`) e re-animação a cada atualização de valor — o mesmo efeito do "Items found" de filamentphp.com/plugins
- **odometer** (secundário): efeito clássico do odometer.js via `gsferro/odometer-easy`, com 7 temas visuais e injeção automática do jQuery nos painéis

### Configuração

- `FilamentOdometerEasyPlugin` com API fluente: `driver()`, `locales()`, `format()`, `theme()` e `jquery()`
- Config publicável (`filament-odometer-easy.php`) com as mesmas opções
- `->format()` polimórfico nos componentes: array = opções Intl.NumberFormat (number-flow), string = data-format (odometer)
- Assets registrados via `FilamentAsset` — instalação em 2 comandos, sem npm e sem `vendor:publish`

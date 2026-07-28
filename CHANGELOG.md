# Changelog

All notable changes to `filament-odometer-easy` will be documented in this file.

## v1.0 - 2026-07-27

Primeira release. 🎉

### Componentes

- `OdometerColumn` (tabelas), `OdometerEntry` (infolists) e `OdometerStat` (StatsOverviewWidget) com contadores animados, herdando 100% da API dos componentes base do Filament v5
- Facade `FilamentOdometerEasy` com `render()`, `renderNumberFlow()` e `renderOdometer()` para uso em qualquer view

### Drivers

- **number-flow** (padrão): web component sem dependências, bundlado no pacote (~17 KB), com formatação via `Intl.NumberFormat`, suporte a locale (`pt-BR`) e acessibilidade (`prefers-reduced-motion`) — o mesmo efeito do "Items found" de filamentphp.com/plugins
- Animação de entrada garantida: exibe 0 e, após um `delay` configurável (500ms), anima até o valor
- Re-animação a cada atualização de valor (Livewire poll/morph) via `MutationObserver` nos data-attributes
- Velocidade da animação controlável com `duration` (config, plugin ou `->duration()` por componente)
- **odometer** (secundário): efeito clássico do odometer.js via `gsferro/odometer-easy`, com 7 temas visuais e injeção automática do jQuery nos painéis

### Configuração

- `FilamentOdometerEasyPlugin` com API fluente: `driver()`, `locales()`, `format()`, `theme()` e `jquery()`
- Config publicável (`filament-odometer-easy.php`) com as mesmas opções
- `->format()` polimórfico nos componentes: array = opções Intl.NumberFormat (number-flow), string = data-format (odometer)
- Assets registrados via `FilamentAsset` — instalação em 2 comandos, sem npm e sem `vendor:publish`

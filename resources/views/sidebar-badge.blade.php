{{--
    Badge de navegação visível com a sidebar recolhida no desktop.

    Injetado no <head> por render hook (FilamentOdometerEasyServiceProvider) apenas quando
    `filament-odometer-easy.badge-on-collapsed-sidebar` está ligado. Vem inline, e não como
    asset, de propósito: são ~600 bytes e assim o recurso não passa a exigir
    `php artisan filament:assets` em todo deploy.

    O problema: com `->sidebarCollapsibleOnDesktop()`, o Filament esconde o badge do item de
    menu — `.fi-sidebar-item-badge-ctn` carrega `x-show="$store.sidebar.isOpen"`
    (filament/filament: views/components/sidebar/item.blade.php), que escreve `display:none`
    inline. A contagem some justamente no modo em que só há ícone. Não existe prop, config nem
    render hook por item no Filament para mudar isso.

    A saída é a mesma que o Filament já usa no gatilho de filtros da tabela: badge flutuante
    ancorado no canto do ícone, com fundo sólido no CONTAINER recortando a borda
    (filament/support: css/components/icon-button.css). Sem Blade publicado e sem JS, porque os
    dois ganchos já existem: `#fi-main-sidebar` expõe o estado como classe (`fi-sidebar-open`,
    views/livewire/sidebar.blade.php) e `.fi-sidebar-item-btn` já é `relative`.

    `!important` no display é o que vence a declaração inline do x-show. Escopo cirúrgico — só
    desktop, só sidebar fechada — para que o estado aberto continue 100% nativo.

    64rem = o breakpoint do store do Alpine do Filament; abaixo dele a sidebar fechada está
    fora da tela e a regra não deve valer.
--}}
<style>
    @media (width >= 64rem) {
        .fi-main-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-badge-ctn {
            display: flex !important;
            position: absolute;
            inset-inline-start: 100%;
            top: 0.25rem;
            width: max-content;
            transform: translate(-50%, -50%);
            border-radius: 0.375rem;
            background-color: var(--color-white, #fff);
        }

        [dir='rtl'] .fi-main-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-badge-ctn {
            transform: translate(50%, -50%);
        }

        .dark .fi-main-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-badge-ctn {
            background-color: var(--gray-900, #111827);
        }

        /*
         | Tamanho `xs`, como o badge do botão de filtro (`Size::ExtraSmall` é o badgeSize
         | padrão de button/icon-button). O badge de menu é `md` e, sem isto, fica mais largo
         | que o próprio ícone.
         |
         | Teto conhecido: a folga à direita do item é ~16px (`.fi-sidebar-nav` é
         | `overflow-x:hidden` com `px-6`; `.fi-sidebar-nav-groups` tem `-mx-2`), então
         | contagem de 5+ dígitos perde 1-2px na borda. Saída: `notation: 'compact'` no
         | `format` do number-flow, ou reduzir o `-50%` do translate no seu tema.
         */
        .fi-main-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-badge-ctn .fi-badge {
            min-width: 1rem;
            padding-block: 0;
            padding-inline: 0.125rem;
            letter-spacing: -0.025em;
        }
    }
</style>

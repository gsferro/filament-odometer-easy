<?php

// config for Gsferro/FilamentOdometerEasy
return [

    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | Motor de animação usado pelos componentes:
    |
    | - "number-flow" (padrão): web component moderno, sem dependências,
    |   acessível, com formatação via Intl.NumberFormat e re-animação a cada
    |   atualização de valor (Livewire/polling). É o mesmo efeito usado em
    |   filamentphp.com/plugins.
    |
    | - "odometer": efeito clássico do odometer.js via gsferro/odometer-easy.
    |   Depende do jQuery (injetado automaticamente) e não re-anima após a
    |   primeira renderização.
    |
    */

    'driver' => 'number-flow',

    /*
    |--------------------------------------------------------------------------
    | number-flow
    |--------------------------------------------------------------------------
    |
    | locales: locale para formatação (ex.: 'pt-BR' => 1.000,00).
    |          null usa o locale do navegador.
    |
    | format:  opções do Intl.NumberFormat aplicadas por padrão.
    |          Ex.: ['style' => 'currency', 'currency' => 'BRL']
    |          Ex.: ['minimumFractionDigits' => 2]
    |
    | delay:   espera (ms) após o componente carregar antes de animar de 0
    |          até o valor no primeiro render. Não afeta atualizações
    |          posteriores (poll/Livewire), que animam imediatamente.
    |
    | duration: velocidade da animação em ms (quanto maior, mais lenta).
    |           null usa os timings padrão do number-flow (~900ms).
    |
    */

    'number-flow' => [
        'locales' => null,
        'format' => null,
        'delay' => 500,
        'duration' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | odometer
    |--------------------------------------------------------------------------
    |
    | theme:  default, car, digital, minimal, plaza, slot-machine, train-station.
    |
    | format: data-format padrão; null usa o padrão do odometer-easy
    |         (pt-BR: 1.000,00). Ex.: '(,ddd)', '(,ddd).dd', '(.ddd),dd', 'd'.
    |
    | jquery: o odometer-easy.js depende do jQuery e o Filament não o carrega
    |         por padrão, então o plugin injeta o script no <head> dos painéis.
    |         Se a sua aplicação já o carrega, desative aqui. Ao trocar o "src",
    |         atualize ou remova (null) o "integrity".
    |
    */

    'odometer' => [
        'theme' => 'default',
        'format' => null,

        'jquery' => [
            'enabled' => true,
            'src' => 'https://code.jquery.com/jquery-4.0.0.min.js',
            'integrity' => 'sha256-OaVG6prZf4v69dPg6PhVattBXkcOWQB62pdZ3ORyrao=',
        ],
    ],
];

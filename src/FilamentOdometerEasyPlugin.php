<?php

namespace Gsferro\FilamentOdometerEasy;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentOdometerEasyPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-odometer-easy';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * Motor de animação: 'number-flow' (padrão) ou 'odometer'.
     */
    public function driver(string $driver): static
    {
        config(['filament-odometer-easy.driver' => $driver]);

        return $this;
    }

    /**
     * Mantém o navigation badge visível quando a sidebar está recolhida no desktop
     * (`->sidebarCollapsibleOnDesktop()`), no formato flutuante que o Filament já usa no
     * gatilho de filtros da tabela. Com a sidebar aberta, nada muda.
     *
     * Opt-in porque altera a aparência do menu de quem só atualizou a versão.
     */
    public function badgeOnCollapsedSidebar(bool $enabled = true): static
    {
        config(['filament-odometer-easy.badge-on-collapsed-sidebar' => $enabled]);

        return $this;
    }

    /**
     * Locale de formatação do number-flow (ex.: 'pt-BR' => 1.000,00).
     */
    public function locales(?string $locales): static
    {
        config(['filament-odometer-easy.number-flow.locales' => $locales]);

        return $this;
    }

    /**
     * Espera (ms) antes da animação inicial do number-flow: o componente
     * exibe 0 e, após o delay, anima até o valor real.
     */
    public function delay(int $delay): static
    {
        config(['filament-odometer-easy.number-flow.delay' => $delay]);

        return $this;
    }

    /**
     * Velocidade da animação do number-flow em ms (quanto maior, mais lenta).
     * null usa os timings padrão do number-flow (~900ms).
     */
    public function duration(?int $duration): static
    {
        config(['filament-odometer-easy.number-flow.duration' => $duration]);

        return $this;
    }

    /**
     * Formato padrão para todos os componentes, de acordo com o driver:
     * - array => opções Intl.NumberFormat do number-flow,
     *   ex.: ['style' => 'currency', 'currency' => 'BRL']
     * - string => data-format do odometer, ex.: '(.ddd),dd'
     *
     * @param  string|array<string, mixed>|null  $format
     */
    public function format(string | array | null $format): static
    {
        $key = is_array($format) ? 'number-flow.format' : 'odometer.format';

        config(["filament-odometer-easy.{$key}" => $format]);

        return $this;
    }

    /**
     * Tema do driver odometer: default, car, digital, minimal, plaza,
     * slot-machine ou train-station.
     */
    public function theme(string $theme): static
    {
        config(['filament-odometer-easy.odometer.theme' => $theme]);

        return $this;
    }

    /**
     * Sobrescreve config('filament-odometer-easy.odometer.jquery').
     * Use enabled: false quando a aplicação já carrega o jQuery.
     */
    public function jquery(bool $enabled = true, ?string $src = null, ?string $integrity = null): static
    {
        config(['filament-odometer-easy.odometer.jquery.enabled' => $enabled]);

        if ($src !== null) {
            config([
                'filament-odometer-easy.odometer.jquery.src' => $src,
                'filament-odometer-easy.odometer.jquery.integrity' => $integrity,
            ]);
        }

        return $this;
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}

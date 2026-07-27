<?php

namespace Gsferro\FilamentOdometerEasy;

use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Gsferro\FilamentOdometerEasy\Testing\TestsFilamentOdometerEasy;
use Gsferro\OdometerEasy\Providers\OdometerEasyServiceProvider;
use Livewire\Features\SupportTesting\Testable;
use ReflectionClass;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentOdometerEasyServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-odometer-easy';

    public static string $viewNamespace = 'filament-odometer-easy';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews(static::$viewNamespace)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('gsferro/filament-odometer-easy');
            });
    }

    public function packageBooted(): void
    {
        // Assets do driver ativo, copiados para public/ pelo comando
        // filament:assets e carregados em todos os painéis.
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        // Somente para o driver "odometer": o odometer-easy.js depende do
        // jQuery, que o Filament não carrega por padrão.
        $this->registerJqueryRenderHook();

        // Testing
        Testable::mixin(new TestsFilamentOdometerEasy);
    }

    protected function getDriver(): string
    {
        return config('filament-odometer-easy.driver', 'number-flow');
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        if ($this->getDriver() === 'odometer') {
            $publicPath = $this->getOdometerEasyPublicPath();

            return [
                Css::make('odometer-easy-theme', "{$publicPath}/odometer/themes/{$this->getThemeFileName()}"),
                Js::make('odometer', "{$publicPath}/odometer/odometer.js"),
                Js::make('odometer-easy', "{$publicPath}/odometer-easy.js"),
            ];
        }

        // number-flow (padrão): bundle próprio do pacote, sem dependências.
        return [
            Js::make('filament-odometer-easy', __DIR__ . '/../resources/dist/filament-odometer-easy.js')->module(),
        ];
    }

    protected function registerJqueryRenderHook(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            function (): string {
                // Avaliado apenas na renderização, para respeitar overrides
                // feitos via FilamentOdometerEasyPlugin depois do boot.
                if ($this->getDriver() !== 'odometer') {
                    return '';
                }

                if (! config('filament-odometer-easy.odometer.jquery.enabled', true)) {
                    return '';
                }

                $src = config('filament-odometer-easy.odometer.jquery.src');

                if (blank($src)) {
                    return '';
                }

                $integrity = config('filament-odometer-easy.odometer.jquery.integrity');
                $attributes = filled($integrity)
                    ? sprintf(' integrity="%s" crossorigin="anonymous"', e($integrity))
                    : '';

                return sprintf('<script src="%s"%s></script>', e($src), $attributes);
            }
        );
    }

    protected function getAssetPackageName(): ?string
    {
        return 'gsferro/filament-odometer-easy';
    }

    /**
     * Diretório src/public do pacote gsferro/odometer-easy, resolvido pela
     * própria classe para não depender do caminho do vendor.
     */
    protected function getOdometerEasyPublicPath(): string
    {
        $srcPath = dirname((new ReflectionClass(OdometerEasyServiceProvider::class))->getFileName(), 2);

        return "{$srcPath}/public";
    }

    protected function getThemeFileName(): string
    {
        $theme = config('filament-odometer-easy.odometer.theme', 'default') ?? 'default';

        return $theme === 'default'
            ? 'odometer.css'
            : "odometer-theme-{$theme}.css";
    }
}

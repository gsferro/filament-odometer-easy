<?php

namespace Gsferro\FilamentOdometerEasy\Navigation;

use Gsferro\FilamentOdometerEasy\FilamentOdometerEasy;

/**
 * Badge de navegação animado para o menu do painel:
 *
 *     public static function getNavigationBadge(): ?string
 *     {
 *         return OdometerNavigationBadge::make(static::getModel()::count());
 *     }
 *
 * Também funciona em NavigationItem::badge() e nos badges de sub-navegação.
 * A formatação (locale, moeda etc.) vem da config global do number-flow.
 */
class OdometerNavigationBadge
{
    public static function make(mixed $value): string
    {
        return app(FilamentOdometerEasy::class)->renderNavigationBadge($value);
    }
}

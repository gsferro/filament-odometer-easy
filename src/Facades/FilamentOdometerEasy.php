<?php

namespace Gsferro\FilamentOdometerEasy\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\HtmlString;

/**
 * @method static HtmlString render(mixed $value, string|array|null $format = null, ?string $class = null, ?string $driver = null, ?int $duration = null)
 * @method static HtmlString renderNumberFlow(mixed $value, ?array $format = null, ?string $class = null, ?int $delay = null, ?int $duration = null)
 * @method static HtmlString renderOdometer(mixed $value, ?string $format = null, ?string $class = null)
 *
 * @see \Gsferro\FilamentOdometerEasy\FilamentOdometerEasy
 */
class FilamentOdometerEasy extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Gsferro\FilamentOdometerEasy\FilamentOdometerEasy::class;
    }
}

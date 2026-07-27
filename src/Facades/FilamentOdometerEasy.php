<?php

namespace Gsferro\FilamentOdometerEasy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Gsferro\FilamentOdometerEasy\FilamentOdometerEasy
 */
class FilamentOdometerEasy extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Gsferro\FilamentOdometerEasy\FilamentOdometerEasy::class;
    }
}

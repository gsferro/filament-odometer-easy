<?php

namespace Gsferro\FilamentOdometerEasy\Commands;

use Illuminate\Console\Command;

class FilamentOdometerEasyCommand extends Command
{
    public $signature = 'filament-odometer-easy';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

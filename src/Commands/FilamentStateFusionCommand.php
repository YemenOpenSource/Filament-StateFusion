<?php

namespace A909M\FilamentStateFusion\Commands;

use Illuminate\Console\Command;

class FilamentStateFusionCommand extends Command
{
    public $signature = 'filament-statefusion';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

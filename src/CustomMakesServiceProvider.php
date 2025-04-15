<?php

namespace DanieleMontecchi\CustomMakes;

use DanieleMontecchi\CustomMakes\Console\CreateMakeCommand;
use DanieleMontecchi\CustomMakes\Console\ListCustomMakesCommand;
use DanieleMontecchi\CustomMakes\Console\MakeCustomCommand;
use Illuminate\Support\ServiceProvider;

class CustomMakesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Optional: merge config
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateMakeCommand::class,
                ListCustomMakesCommand::class,
                MakeCustomCommand::class,
            ]);
        }
    }
}

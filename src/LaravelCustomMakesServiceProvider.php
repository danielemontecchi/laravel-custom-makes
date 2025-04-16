<?php

namespace DanieleMontecchi\LaravelCustomMakes;

use DanieleMontecchi\LaravelCustomMakes\Console\CreateMakeCommand;
use DanieleMontecchi\LaravelCustomMakes\Console\ListLaravelCustomMakesCommand;
use DanieleMontecchi\LaravelCustomMakes\Console\MakeCustomCommand;
use Illuminate\Support\ServiceProvider;

class LaravelCustomMakesServiceProvider extends ServiceProvider
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
                ListLaravelCustomMakesCommand::class,
                MakeCustomCommand::class,
            ]);
        }
    }
}

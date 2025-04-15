<?php

namespace DanieleMontecchi\CustomMakes;

use DanieleMontecchi\CustomMakes\Console\CreateMakeCommand;
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
            ]);
        }
    }
}

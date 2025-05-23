<?php

namespace DanieleMontecchi\LaravelCustomMakes;

use DanieleMontecchi\LaravelCustomMakes\Console\MakeCustomListCommand;
use DanieleMontecchi\LaravelCustomMakes\Console\MakeCustomCommand;
use Illuminate\Support\ServiceProvider;

class LaravelCustomMakesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-custom-makes.php', 'laravel-custom-makes');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeCustomListCommand::class,
                MakeCustomCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../config/laravel-custom-makes.php' => config_path('laravel-custom-makes.php'),
        ], 'laravel-custom-makes-config');
    }
}

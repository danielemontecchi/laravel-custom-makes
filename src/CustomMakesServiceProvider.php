<?php

namespace DanieleMontecchi\CustomMakes;

use Illuminate\Support\ServiceProvider;

class CustomMakesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Optional: merge config
    }

    public function boot(): void
    {
        // Register commands, config, stub publishing, etc.
    }
}

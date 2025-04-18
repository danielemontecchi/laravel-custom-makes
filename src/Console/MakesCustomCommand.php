<?php

namespace DanieleMontecchi\LaravelCustomMakes\Console;

use DanieleMontecchi\LaravelCustomMakes\Support\GeneratorDefinition;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Command to generate a class from a custom stub definition.
 * Falls back to Laravel native stubs if no custom stub is found (optional).
 */
class MakesCustomCommand extends Command
{
    /**
     * The command signature.
     */
    protected $signature = 'make:custom
                            {type? : The type of generator to use (e.g. services)}
                            {name? : The name of the class to generate}
                            {--force|-f : Overwrite the file if it already exists}';

    /**
     * The command description.
     */
    protected $description = 'Generate a class using a custom stub, with optional fallback to Laravel native stubs.';

    /**
     * Handle the command execution.
     */
    public function handle(): int
    {
        $type = $this->argument('type') ?? $this->ask('What is the generator type? (e.g. services)');
        $name = $this->argument('name') ?? $this->ask('What should the class name be?');

        // Resolve stub path (custom or fallback)
        $stubPath = GeneratorDefinition::pathStub($type);

        if (!file_exists($stubPath)) {
            if (config('custom-makes.allow_laravel_stub_fallback', true)) {
                $fallbackStub = base_path("stubs/{$type}.stub");

                if (!file_exists($fallbackStub)) {
                    $this->components->error("Stub not found for type [{$type}].");
                    return self::FAILURE;
                }

                $stubPath = $fallbackStub;
                $this->components->warn("Fallback to Laravel native stub: stubs/{$type}.stub");
            } else {
                $this->components->error("Stub not found for generator type [{$type}].");
                return self::FAILURE;
            }
        }

        $outputPath = app_path(Str::studly($type));
        $namespace = 'App\\' . Str::studly($type);
        $className = Str::studly($name);
        $targetPath = $outputPath . '/' . $className . '.php';

        // Check for file collision
        if (file_exists($targetPath) && !$this->option('force')) {
            $this->components->warn("File already exists: " . str_replace(base_path() . '/', '', $targetPath));
            if (!$this->confirm('Do you want to overwrite it?', false)) {
                $this->components->info('Generation cancelled.');
                return self::SUCCESS;
            }
        }

        // Create directory if missing
        @mkdir(dirname($targetPath), recursive: true);

        // Replace stub placeholders
        $stub = file_get_contents($stubPath);
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $className],
            $stub
        );

        file_put_contents($targetPath, $stub);

        // Show result
        $this->components->info('File generated successfully');
        $this->components->twoColumnDetail('✔ Class created', str_replace(base_path() . '/', '', $targetPath));

        return self::SUCCESS;
    }
}

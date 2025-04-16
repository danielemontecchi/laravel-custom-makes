<?php

namespace DanieleMontecchi\LaravelCustomMakes\Console;

use DanieleMontecchi\LaravelCustomMakes\Support\GeneratorDefinition;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeCustomCommand extends Command
{
    protected $signature = 'make:custom
                            {type? : The type of generator to use (e.g. services)}
                            {name? : The name of the class to generate}
                            {--force|-f : Overwrite the file if it already exists}';

    protected $description = 'Generate a class using a custom stub';

    public function handle(): int
    {
        $type = $this->argument('type') ?? $this->ask('What is the generator type? (e.g. services)');
        $name = $this->argument('name') ?? $this->ask('What should the class name be?');

        $stubPath = GeneratorDefinition::pathStub($type);

        if (!file_exists($stubPath)) {
            $this->components->error("Stub for generator type [{$type}] not found at: " . str_replace(base_path() . '/', '', $stubPath));
            return self::FAILURE;
        }

        // Costruzione path e namespace a partire dal tipo
        $outputPath = base_path('app/' . Str::studly($type));
        $namespace = 'App\\' . Str::studly($type);
        $className = Str::studly($name);

        $targetPath = $outputPath . '/' . $className . '.php';

        // Check se file esiste
        if (file_exists($targetPath) && !$this->option('force')) {
            $this->components->warn("File already exists: " . str_replace(base_path() . '/', '', $targetPath));
            if (!$this->confirm('Do you want to overwrite it?', false)) {
                $this->components->info('Generation cancelled.');
                return self::SUCCESS;
            }
        }

        // Crea directory se necessario
        @mkdir(dirname($targetPath), recursive: true);

        // Legge lo stub e sostituisce i placeholder
        $stub = file_get_contents($stubPath);
        $stub = str_replace(['{{ namespace }}', '{{ class }}'], [$namespace, $className], $stub);

        file_put_contents($targetPath, $stub);

        // Output finale
        $this->components->info('File generated successfully');
        $this->components->twoColumnDetail('✔ Class created', str_replace(base_path() . '/', '', $targetPath));

        return self::SUCCESS;
    }
}

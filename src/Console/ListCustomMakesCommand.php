<?php

namespace DanieleMontecchi\CustomMakes\Console;

use DanieleMontecchi\CustomMakes\Support\GeneratorDefinition;
use Illuminate\Console\Command;

class ListCustomMakesCommand extends Command
{
    protected $signature = 'list:custom-makes';
    protected $description = 'List all available custom generators';

    public function handle(): int
    {
        $directory = base_path('custom-makes');

        if (!is_dir($directory)) {
            $this->components->warn('No custom generators found.');
            return self::SUCCESS;
        }

        $files = glob($directory . '/*.json');

        if (empty($files)) {
            $this->components->warn('No custom generators found.');
            return self::SUCCESS;
        }

        $this->components->info('📦 Available custom generators:');
        $this->newLine();

        foreach ($files as $file) {
            $key = basename($file, '.json');

            try {
                $definition = GeneratorDefinition::fromArray(
                    json_decode(file_get_contents($file), true)
                );

                $this->components->twoColumnDetail(
                    '✔ ' . str_pad($key, 15),
                    "{$definition->command}  ({$definition->outputPath})"
                );
            } catch (\Throwable $e) {
                $this->components->error("✘ Failed to read: {$key}.json");
            }
        }

        return self::SUCCESS;
    }
}

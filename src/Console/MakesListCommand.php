<?php

namespace DanieleMontecchi\LaravelCustomMakes\Console;

use DanieleMontecchi\LaravelCustomMakes\Support\GeneratorDefinition;
use Illuminate\Console\Command;

class MakesListCommand extends Command
{
    protected $signature = 'makes:list
                            {--json : Show only generators that have a JSON definition}';

    protected $description = 'List all available custom generators based on stub files';

    public function handle(): int
    {
        $stubDir = GeneratorDefinition::pathStub();
        $jsonDir = GeneratorDefinition::pathDefinition();

        if (!is_dir($stubDir)) {
            $this->components->warn('No custom generators found.');
            return self::SUCCESS;
        }

        $stubFiles = glob($stubDir . '/*.stub');
        if (empty($stubFiles)) {
            $this->components->warn('No custom generators found.');
            return self::SUCCESS;
        }

        $onlyJson = $this->option('json');

        $this->components->info('📦 Available custom generators:');
        $this->newLine();

        $shown = 0;

        foreach ($stubFiles as $file) {
            $name = basename($file, '.stub');
            $jsonPath = $jsonDir . '/' . $name . '.json';
            $hasJson = file_exists($jsonPath);

            if ($onlyJson && !$hasJson) {
                continue;
            }

            $info = $hasJson ? 'stub + json' : 'stub only';
            $this->components->twoColumnDetail("✔ " . str_pad($name, 15), $info);
            $shown++;
        }

        if ($shown === 0) {
            $this->components->warn($onlyJson ? 'No generators with JSON found.' : 'No generators found.');
        }

        return self::SUCCESS;
    }
}

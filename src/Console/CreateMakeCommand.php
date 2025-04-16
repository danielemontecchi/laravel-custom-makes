<?php

namespace DanieleMontecchi\LaravelCustomMakes\Console;

use DanieleMontecchi\LaravelCustomMakes\Support\GeneratorDefinition;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateMakeCommand extends Command
{
    protected $signature = 'create:make
                            {name? : The name, in lowercase, of the generator (e.g. services)}
                            {--json : Also generate a definition JSON file}';

    protected $description = 'Create a new custom make command stub, with optional JSON definition.';

    public function handle(): int
    {
        $commandName = Str::lower($this->argument('name') ?? $this->ask('Enter the command name (e.g. services)'));

        if (Str::plural($commandName) !== $commandName) {
            if (!$this->confirm("It's recommended to use a plural form for the generator name. Do you want to continue anyway?")) {
                $this->components->info('Cancelled.');
                return self::SUCCESS;
            }
        }

        $suffixPath = Str::studly($commandName);
        $outputPath = $this->ask('Where should the file be created?', 'app/' . $suffixPath);

        $defaultNamespace = collect(explode('/', trim($outputPath, '/')))
            ->map(fn($part) => ucfirst($part))
            ->implode('\\');

        $namespace = $this->ask('What is the default namespace?', $defaultNamespace);

        $stubPath = GeneratorDefinition::pathStub($commandName);

        $this->generateDefaultStub($stubPath, $namespace);

        $this->components->twoColumnDetail('✔ Stub created', str_replace(base_path() . '/', '', $stubPath));

        if ($this->option('json')) {
            $definition = new GeneratorDefinition(
                $commandName,
                '{{ name }}',
                $stubPath,
                $outputPath,
                $namespace,
                ['name', 'namespace', 'class']
            );

            $jsonPath = GeneratorDefinition::pathJson($commandName);

            $definition->saveTo($jsonPath);

            $this->components->twoColumnDetail('✔ Generator definition', str_replace(base_path() . '/', '', $jsonPath));
        }

        $this->components->info('Custom generator created successfully.');

        return self::SUCCESS;
    }

    protected function generateDefaultStub(string $path, string $namespace): void
    {
        $stub = <<<PHP
<?php

namespace {{ namespace }};

class {{ class }}
{
    //
}
PHP;
        @mkdir(dirname($path), recursive: true);
        file_put_contents($path, $stub);
    }
}

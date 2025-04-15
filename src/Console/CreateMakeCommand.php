<?php

namespace DanieleMontecchi\CustomMakes\Console;

use DanieleMontecchi\CustomMakes\Support\GeneratorDefinition;
use Illuminate\Console\Command;

class CreateMakeCommand extends Command
{
    protected $signature = 'create:make';
    protected $description = 'Create a new custom make command';

    public function handle(): int
    {
        $commandName = $this->ask('Enter the command name (e.g. make:service)');

        $suffixPath = ucfirst(last(explode(':', $commandName)));
        $outputPath = $this->ask('Where should the file be created?', 'app/' . $suffixPath);

        $defaultNamespace = collect(explode('/', trim($outputPath, '/')))
            ->map(fn($part) => ucfirst($part))
            ->implode('\\');

        $namespace = $this->ask('What is the default namespace?', $defaultNamespace);

        $stubPath = base_path('stubs/custom/' . str_replace(':', '-', $commandName) . '.stub');
        $filename = base_path('custom-makes/' . str_replace(':', '-', $commandName) . '.json');

        $this->generateDefaultStub($stubPath, $namespace);

        $definition = new GeneratorDefinition(
            command: $commandName,
            namePlaceholder: '{{ name }}',
            stubPath: $stubPath,
            outputPath: $outputPath,
            namespace: $namespace,
            variables: ['name', 'namespace', 'class']
        );

        @mkdir(dirname($filename), recursive: true);
        $definition->saveTo($filename);

        $this->components->info('Generator created successfully');
        $this->components->twoColumnDetail('✔ Stub created', str_replace(base_path() . '/', '', $stubPath));
        $this->components->twoColumnDetail('✔ Generator definition', str_replace(base_path() . '/', '', $filename));

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

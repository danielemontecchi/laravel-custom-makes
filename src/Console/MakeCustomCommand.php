<?php

namespace DanieleMontecchi\CustomMakes\Console;

use DanieleMontecchi\CustomMakes\Support\GeneratorDefinition;
use Illuminate\Console\GeneratorCommand;

class MakeCustomCommand extends GeneratorCommand
{
    protected $name = 'make:custom';
    protected $description = 'Generate a class using a custom generator definition';
    protected $type = 'CustomClass';

    public function handle(): int
    {
        $generatorType = $this->argument('type') ?? $this->ask('What custom generator do you want to use?');
        $className = $this->argument('name') ?? $this->ask('What should the class name be?');

        $definitionPath = base_path("custom-makes/{$generatorType}.json");

        if (!file_exists($definitionPath)) {
            $this->components->error("Custom generator [{$generatorType}] not found.");
            return self::FAILURE;
        }

        $definition = GeneratorDefinition::fromArray(json_decode(file_get_contents($definitionPath), true));

        // Imposta proprietà necessarie per il GeneratorCommand
        $this->stubPath = $definition->stubPath;
        $this->outputPath = $definition->outputPath;
        $this->defaultNamespace = $definition->namespace;
        $this->nameArgument = $className;

        return parent::handle();
    }

    protected function getStub()
    {
        return $this->stubPath;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->defaultNamespace;
    }

    protected function getPath($name)
    {
        return base_path(
            $this->outputPath . '/' . class_basename($name) . '.php'
        );
    }

    protected function getNameInput()
    {
        return $this->nameArgument;
    }
}

<?php

namespace DanieleMontecchi\CustomMakes\Support;

class GeneratorDefinition
{
    public function __construct(
        public string $command,
        public string $namePlaceholder,
        public string $stubPath,
        public string $outputPath,
        public string $namespace,
        public array  $variables = []
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['command'],
            $data['name_placeholder'],
            $data['stub_path'],
            $data['output_path'],
            $data['namespace'],
            $data['variables'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'command' => $this->command,
            'name_placeholder' => $this->namePlaceholder,
            'stub_path' => $this->stubPath,
            'output_path' => $this->outputPath,
            'namespace' => $this->namespace,
            'variables' => $this->variables,
        ];
    }

    public function saveTo(string $path): void
    {
        file_put_contents($path, json_encode($this->toArray(), JSON_PRETTY_PRINT));
    }
}

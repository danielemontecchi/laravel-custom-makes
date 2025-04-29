<?php

namespace DanieleMontecchi\LaravelCustomMakes\Support;

use Illuminate\Support\Str;

/**
 * Represents the definition for a generator, encapsulating information
 * about the command, placeholders, stub paths, and output paths.
 */
class GeneratorDefinition
{
    public function __construct(
        public string $command,
        public string $namePlaceholder,
        public string $stubPath,
        public string $outputPath,
        public string $namespace,
        public array  $variables = [],
    )
    {
    }

    /**
     * Creates a new instance of the class using an associative array.
     *
     * @param array $data An associative array containing the required keys:
     *                    'command', 'name_placeholder', 'stub_path', 'output_path', 'namespace',
     *                    and an optional key 'variables'.
     * @return self Returns a new instance of the class initialized with the provided data.
     */
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

    /**
     * Converts the current instance of the class into an associative array.
     *
     * @return array Returns an array representation of the object, containing the keys:
     *               'command', 'name_placeholder', 'stub_path', 'output_path', 'namespace',
     *               and 'variables'.
     */
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

    /**
     * Saves the current object data to a specified file path in JSON format.
     *
     * @param string $path The file path where the object data will be saved.
     *                     Intermediate directories will be created if they do not exist.
     * @return void
     */
    public function saveTo(string $path): void
    {
        @mkdir(dirname($path), recursive: true);
        file_put_contents($path, json_encode($this->toArray(), JSON_PRETTY_PRINT));
    }

    /**
     * Generates the full file path for a specific stub file based on the provided type.
     *
     * @param string $type The type of the stub file. If empty, no specific file will be appended.
     * @return string The full path to the stub file or folder.
     */
    public static function pathStub(string $type = ''): string
    {
        $stubFilePath = Str::finish(config('laravel-custom-makes.stubs_path', 'stubs'), '/');
        $stubFilePath .= !empty($type)
            ? Str::kebab($type) . '.stub'
            : '';

        return base_path($stubFilePath);
    }
}

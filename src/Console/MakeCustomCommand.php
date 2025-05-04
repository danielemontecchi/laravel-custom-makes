<?php

namespace DanieleMontecchi\LaravelCustomMakes\Console;

use DanieleMontecchi\LaravelCustomMakes\Support\GeneratorDefinition;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Command to generate a new class using a custom stub or create the stub itself.
 *
 * - If both type and name are provided, a class is generated using the resolved stub.
 * - If only the type is provided, a stub file is created under the custom stubs path.
 */
class MakeCustomCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:custom
                            {type : The generator type (e.g. service)}
                            {name? : The class name (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a stub file or generate a class from stub.';

    /**
     * Path to the resolved stub file.
     *
     * @var string|null
     */
    protected string|null $stubPath = null;

    /**
     * Handle the command execution.
     *
     * @return int
     */
    public function handle(): bool|null
    {
        $this->type = Str::pascal($this->argument('type'));
        $this->name = $this->argument('name');
        $this->stubPath = $this->getStub();

        return (empty($this->name))
            ? $this->createFileStub()
            : parent::handle();
    }

    /**
     * Get the stub file path.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return GeneratorDefinition::pathStub($this->type);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\\' . Str::plural($this->type);
    }

    /**
     * If it does not already exist, generate the stub file using a standard template.
     *
     * @return int
     */
    protected function createFileStub(): int
    {
        $absStubPath = Str::remove(Str::finish(base_path(), '/'), $this->stubPath, false);
        if (File::exists($this->stubPath)) {
            $this->components->error("The stub already exists: {$absStubPath}");
            return self::FAILURE;
        }

        // Create the directory if it doesn't exist
        $stubDir = File::dirname($this->stubPath);
        if (!File::exists($stubDir)) {
            File::makeDirectory($stubDir);
        }

        // Try to load native Laravel stub if available
        $stubName = Str::kebab($this->type) . '.stub';
        $nativeStub = base_path("vendor/laravel/framework/src/Illuminate/Foundation/Console/stubs/{$stubName}");
        $stubContent = (File::exists($nativeStub))
            ? File::get($nativeStub)
            : $this->defaultStubTemplate();
        File::put($this->stubPath, $stubContent);
        $this->components->twoColumnDetail('✔ Stub created', $absStubPath);

        return self::SUCCESS;
    }

    /**
     * Return the default template for a new stub file.
     *
     * @return string
     */
    protected function defaultStubTemplate(): string
    {
        return <<<PHP
<?php

namespace {{ namespace }};

class {{ class }}
{
}
PHP;
    }
}

<?php

namespace DanieleMontecchi\LaravelCustomMakes\Console;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

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
        $this->name = Str::pascal($this->argument('name'));
        $this->stubPath = $this->resolveStubPath();

        return (empty($this->name))
            ? $this->generateStub()
            : parent::handle();
    }

    /**
     * Get the stub file path.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->stubPath ?? '';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\\' . $this->type;
    }

    /**
     * If it does not already exist, generate the stub file using a standard template.
     *
     * @return int
     */
    protected function generateStub(): int
    {
        $absStubPath = Str::remove(Str::finish(base_path(), '/'), $this->stubPath);
        if (File::exists($this->stubPath)) {
            $this->components->error("The stub already exists: {$absStubPath}");
            return self::FAILURE;
        }

        $stubDir = File::dirname($this->stubPath);
        if (!File::exists($stubDir)) {
            File::makeDirectory($stubDir);
        }
        File::put($this->stubPath, $this->defaultStubTemplate());
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

namespace DummyNamespace;

class DummyClass
{
}
PHP;
    }

    /**
     * Resolve the appropriate stub path for the given type.
     *
     * Order of resolution:
     * - custom-makes/stubs/{Type}.stub
     * - stubs/{type}.stub
     * - Laravel's native stub
     *
     * @return string
     */
    protected function resolveStubPath(): string
    {
        $fileName = Str::kebab($this->type) . '.stub';
        $filePath = base_path("stubs/$fileName");
        if (File::exists($filePath)) return $filePath;

        $fallback = base_path("vendor/laravel/framework/src/Illuminate/Foundation/Console/stubs/$fileName");
        return (File::exists($fallback))
            ? $fallback
            : $filePath;
    }
}

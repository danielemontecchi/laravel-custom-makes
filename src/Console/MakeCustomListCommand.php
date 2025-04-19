<?php

namespace DanieleMontecchi\LaravelCustomMakes\Console;

use DanieleMontecchi\LaravelCustomMakes\Support\GeneratorDefinition;
use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * This command lists all custom generator stubs located in the custom-makes/stubs directory.
 * It excludes stubs that are used by Laravel's native make:* commands.
 */
class MakeCustomListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:custom-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all custom generator stubs (excluding Laravel native stubs).';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $customStubDir = Str::finish(base_path('stubs'), '/');
        $allCustomStubs = collect(glob($customStubDir . '*.stub'))->map(fn($path) => realpath($path));

        if ($allCustomStubs->isEmpty()) {
            $this->components->info('No custom stubs found in: ' . $customStubDir);
            return self::SUCCESS;
        }

        $laravelStubPaths = $this->getLaravelStubPaths();

        $customOnly = $allCustomStubs->filter(fn($path) => !$laravelStubPaths->contains($path));

        if ($customOnly->isEmpty()) {
            $this->components->info('No custom stubs found (all match Laravel native stubs).');
            return self::SUCCESS;
        }

        $this->components->info('Custom generator stubs found:');
        foreach ($customOnly as $path) {
            $type = Str::of(basename($path))->beforeLast('.stub')->kebab();
            $label = str_pad("  make:custom {$type}", 26); // Align spacing
            $this->line("{$label} Create a new {$type} class");
        }

        return self::SUCCESS;
    }

    /**
     * Get all stub paths used by Laravel's native GeneratorCommand classes.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getLaravelStubPaths(): Collection
    {
        return collect(app()->make('Illuminate\Contracts\Console\Kernel')->all())
            ->filter(fn($cmd, $key) => str_starts_with($key, 'make:') && $cmd instanceof GeneratorCommand)
            ->map(fn($command) => GeneratorDefinition::pathStub(Str::remove('make:', $command->getName())) ?? null)
            ->filter()
            ->unique();
    }
}

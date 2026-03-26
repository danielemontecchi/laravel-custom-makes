<?php

use Illuminate\Support\Facades\File;

const SERVICES_DIR = 'app/Services';
const SERVICE_STUB = 'stubs/service.stub';
const TEST_SERVICE_FILE = 'app/Services/TestService.php';

beforeEach(function () {
    File::deleteDirectory(base_path('stubs'));
    File::deleteDirectory(base_path(SERVICES_DIR));
});

afterEach(function () {
    File::deleteDirectory(base_path('stubs'));
    File::deleteDirectory(base_path(SERVICES_DIR));
});

it('creates a stub file when only type is passed', function () {
    $stubPath = base_path(SERVICE_STUB);

    $exitCode = Artisan::call('make:custom', ['type' => 'service']);

    expect($exitCode)->toBe(0)
        ->and(File::exists($stubPath))->toBeTrue()
        ->and(File::get($stubPath))->toContain('{{ class }}');
});

it('does not overwrite existing stub file', function () {
    $stubPath = base_path(SERVICE_STUB);
    File::ensureDirectoryExists(base_path('stubs'));
    File::put($stubPath, '// existing content');

    $exitCode = Artisan::call('make:custom', ['type' => 'service']);

    expect($exitCode)->toBe(1)
        ->and(File::get($stubPath))->toBe('// existing content');
});

it('generates a class when name is provided', function () {
    File::ensureDirectoryExists(base_path('stubs'));
    File::put(base_path(SERVICE_STUB), "<?php\n\nnamespace {{ namespace }};\n\nclass {{ class }}\n{\n}\n");

    $file = base_path(TEST_SERVICE_FILE);

    $exitCode = Artisan::call('make:custom', ['type' => 'service', 'name' => 'TestService']);

    expect($exitCode)->toBe(0)
        ->and(File::exists($file))->toBeTrue()
        ->and(File::get($file))->toContain('namespace App\\Services')
        ->and(File::get($file))->toContain('class TestService');
});

it('does not overwrite an existing class', function () {
    File::ensureDirectoryExists(base_path('stubs'));
    File::put(base_path(SERVICE_STUB), "<?php\n\nnamespace {{ namespace }};\n\nclass {{ class }}\n{\n}\n");
    File::ensureDirectoryExists(base_path(SERVICES_DIR));
    File::put(base_path(TEST_SERVICE_FILE), '<?php // existing');

    Artisan::call('make:custom', ['type' => 'service', 'name' => 'TestService']);

    expect(File::get(base_path(TEST_SERVICE_FILE)))->toBe('<?php // existing');
});

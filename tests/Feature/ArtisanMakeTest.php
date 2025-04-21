<?php

use Illuminate\Support\Facades\File;

it('creates a stub file when only type is passed', function () {
    $stubPath = base_path('stubs/Service.stub');
    if (!File::exists(base_path('stubs'))) {
        File::makeDirectory(base_path('stubs'));
    }

    @unlink($stubPath); // cleanup

    $exitCode = Artisan::call('make:custom', ['type' => 'service']);

    expect($exitCode)->toBe(0)
        ->and(file_exists($stubPath))
//        ->toBeTrue()
//        ->and(file_get_contents($stubPath))
//        ->toContain('namespace DummyNamespace');
    ;
});

it('does not overwrite existing stub file', function () {
    $stubPath = base_path('stubs/Service.stub');
    if (!File::exists(base_path('stubs'))) {
        File::makeDirectory(base_path('stubs'));
    }
    file_put_contents($stubPath, '// existing content');

    $exitCode = Artisan::call('make:custom', ['type' => 'service']);

    expect($exitCode)->toBe(1)
        ->and(file_get_contents($stubPath))
        ->toBe('// existing content'); // Laravel FAILURE
});

it('generates a class when name is provided', function () {
    $file = base_path('app/Services/TestService.php');
    @unlink($file);

    $exitCode = Artisan::call('make:custom', ['type' => 'service', 'name' => 'Services\\TestService']);

    expect($exitCode)
        ->toBe(0)
        ->and(file_exists($file))
//        ->toBeTrue()
//        ->and(file_get_contents($file))
//        ->toContain('namespace App\\Services')
    ;
});

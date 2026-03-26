<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::deleteDirectory(base_path('stubs'));
});

afterEach(function () {
    File::deleteDirectory(base_path('stubs'));
});

it('reports no custom stubs when the stubs directory is empty', function () {
    $exitCode = Artisan::call('make:custom-list');

    expect($exitCode)->toBe(0)
        ->and(Artisan::output())->toContain('No custom stubs found');
});

it('lists only custom stubs excluding Laravel native ones', function () {
    File::ensureDirectoryExists(base_path('stubs'));
    File::put(base_path('stubs/foo.stub'), 'class {{ class }} {}');

    $exitCode = Artisan::call('make:custom-list');
    $output = Artisan::output();

    expect($exitCode)->toBe(0)
        ->and($output)->toContain('make:custom foo')
        ->and($output)->toContain('Create a new foo class');
});

it('lists multiple custom stubs', function () {
    File::ensureDirectoryExists(base_path('stubs'));
    File::put(base_path('stubs/foo.stub'), 'class {{ class }} {}');
    File::put(base_path('stubs/bar.stub'), 'class {{ class }} {}');

    $exitCode = Artisan::call('make:custom-list');
    $output = Artisan::output();

    expect($exitCode)->toBe(0)
        ->and($output)->toContain('make:custom foo')
        ->and($output)->toContain('make:custom bar');
});

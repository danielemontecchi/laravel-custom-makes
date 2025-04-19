<?php

use Illuminate\Support\Facades\File;

it('lists only custom stubs excluding Laravel native ones', function () {
    $customStub = base_path('stubs/Foo.stub');
    if (!File::exists(base_path('stubs'))) {
        File::makeDirectory(base_path('stubs'));
    }
    file_put_contents($customStub, 'class {{ class }} {}');

    $output = Artisan::call('make:custom-list');

    $result = Artisan::output();

    expect($result)->toContain('make:custom foo')
        ->and($result)->toContain('Create a new foo class');
});

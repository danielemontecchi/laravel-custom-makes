<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Stub Files Path
    |--------------------------------------------------------------------------
    |
    | This path is used to locate all custom stub files that define how
    | the generated classes should be structured. You may customize it
    | if you want to organize your stub files differently.
    |
    */
    'stubs_path' => 'custom-makes/stubs',

    /*
    |--------------------------------------------------------------------------
    | Generator Definitions Path (JSON)
    |--------------------------------------------------------------------------
    |
    | When using advanced generator definitions via JSON, this path will be
    | used to read and store those files. If unused, you can ignore this.
    |
    */
    'definitions_path' => 'custom-makes/definitions',

    /*
    |--------------------------------------------------------------------------
    | Always Generate JSON Definition
    |--------------------------------------------------------------------------
    |
    | When enabled, this will always generate a JSON definition alongside
    | each stub, even if the --json option is not explicitly provided
    | in the create:make command.
    |
    */
    'always_generate_json' => false,

    /*
    |--------------------------------------------------------------------------
    | Enable fallback to Laravel native stubs
    |--------------------------------------------------------------------------
    |
    | If true, the generator will fallback to Laravel's default stub files
    | (e.g. stubs/class.stub) when a custom stub is not found.
    |
    */
    'allow_laravel_stub_fallback' => true,

];

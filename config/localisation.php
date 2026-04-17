<?php

return [
    'scan' => [
        'paths' => [
            ['path' => base_path('app')],
            ['path' => config_path()],
            ['path' => database_path()],
            ['path' => resource_path()],
            ['path' => base_path('routes')],
            ['path' => base_path('tests')],
            ['path' => base_path('vendor/laravel-enso'), 'exclude' => ['vendor']],
            ['path' => base_path('client/patches')],
            ['path' => base_path('client/src/js')],
            ['path' => base_path('client/node_modules/@enso-ui'), 'exclude' => ['node_modules']],
        ],
        'files' => [
            '*.php',
            '*.blade.php',
            '*.js',
            '*.jsx',
            '*.ts',
            '*.tsx',
            '*.vue',
            '*.patch',
        ],
        'patterns' => [
            '/__\(\s*([\'"])((?:\\\\.|(?!\1).)*?)\1/s',
            '/trans\(\s*([\'"])((?:\\\\.|(?!\1).)*?)\1/s',
            '/@lang\(\s*([\'"])((?:\\\\.|(?!\1).)*?)\1/s',
            '/i18n\(\s*([\'"])((?:\\\\.|(?!\1).)*?)\1/s',
        ],
    ],
];

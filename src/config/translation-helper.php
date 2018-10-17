<?php

return [
    /**
     * Directories to scan for missing translation keys.
     */
    'scan_directories' => [
        app_path(),
        resource_path('views'),
        resource_path('assets'),
    ],

    /**
     * File extensions to scan from.
     */
    'file_extensions' => [
        'php',
        //'js',
        //'vue',
    ],

    /**
     * Directory where your JSON translation files are located.
     */
    'output_directory' => resource_path('lang'),

    /**
     * Translation helper methods to scan
     * for in your application's code.
     */
    'translation_methods' => [
        'lang',
        '__',
    ],
];

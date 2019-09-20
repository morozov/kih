<?php

return [
    'directory_list' => [
        'src',
        'vendor/guzzlehttp/guzzle/src',
        'vendor/psr/http-message/src',
        'vendor/psr/http-server-handler/src',
        'vendor/slim/slim',
    ],
    'exclude_analysis_directory_list' => [
        'vendor',
    ],
    'skip_slow_php_options_warning' => true,
    'suppress_issue_types' => [
        'PhanTypeMismatchDeclaredReturn',
    ],
];

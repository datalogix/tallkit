<?php

return [
    'inject_assets' => true,

    'upload' => [
        'enabled' => true,
        'route' => '/tallkit/upload',
        'middleware' => ['web'],
        'disk' => 'public',
        'directory' => 'tallkit-uploads',
        'max_size' => [
            'image' => 5120,
            'video' => 51200,
            'default' => 20480,
        ],
    ],
];

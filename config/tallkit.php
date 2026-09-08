<?php

return [
    'inject_assets' => true,

    'upload' => [
        'enabled' => true,
        'route' => '/tallkit/upload',
        'middleware' => ['web'],
        'disk' => 'public',
        'directory' => 'tallkit-uploads',
        'allowed_extensions' => [
            'jpg', 'jpeg', 'png', 'gif', 'webp',
            'mp4', 'mov', 'webm',
            'mp3', 'wav',
            'pdf',
            'doc', 'docx',
            'xls', 'xlsx',
            'ppt', 'pptx',
            'zip', 'rar', '7z',
            'txt', 'md', 'csv',
        ],
        'max_size' => [
            'image' => 5120,
            'video' => 51200,
            'default' => 20480,
        ],
    ],
];

<?php

return [
    // 'auto': pages that render a component; true: every HTML page; false: never (use @tallkitScripts).
    'inject_assets' => 'auto',

    'load_alpine' => true,

    'cache_classes' => true,

    // Your own colors: each a .tk-color-{name} class setting the same --tk-* variables as resources/css/colors.css.
    'colors' => [],

    'avatar' => [
        // Sends the email/username to unavatar.io, a third party. false: only URLs show as images.
        'unavatar' => true,
    ],

    // By app locale (or its language): mask => input names, the most specific first. A locale without one gets none.
    'masks' => [
        'pt_BR' => [
            '99999-999' => ['cep', 'zipcode', 'zip-code'],
            '99/99/9999 99:99' => ['datetime', 'date_time', '_at'],
            '99/99/9999' => ['date', 'birthdate', 'birth_date', '_on'],
            '99:99' => ['time'],
            '999.999.999-99' => ['cpf'],
            '**.***.***/****-99' => ['cnpj'],
            '(99) 999999999' => ['tel', 'phone', 'telephone', 'cellphone', 'mobile', 'whatsapp'],
        ],
    ],

    'tooltip' => [
        // Milliseconds.
        'delay' => 200,
        // top, bottom, left or right; start, center or end.
        'position' => 'top',
        'align' => 'center',
        'arrow' => true,
        // null, 'accent' or 'inverse'.
        'variant' => null,
        // null (md), 'xs', 'sm', 'lg' or 'xl'.
        'size' => null,
    ],

    'icon' => [
        // Seconds.
        'missing_ttl' => 60 * 60 * 24,
        // Iconify collections tried in order for a name without one ("home" → "mdi:home", ...).
        'collections' => [
            'mdi',
            'material-symbols',
            'material-symbols-light',
            'ic',
            'ph',
            'solar',
            'tabler',
            'hugeicons',
            'fluent',
            'heroicons',
            'arcticons',
            'openmoji',
            'game-icons',
        ],
    ],

    'upload' => [
        // The editors' uploads (tiptap, quill, tinymce). false: they keep files inline.
        'enabled' => true,
        'route' => '/tallkit/upload',
        'middleware' => ['web', 'throttle:tallkit.uploads'],
        'guard' => null,
        // Minutes.
        'url_ttl' => 60 * 24,
        // Minutes: a guest link is in a page anyone can open.
        'guest_url_ttl' => 60 * 2,
        // What a guest link takes (anyone may use it to store files). Size in KB.
        'guest_types' => ['image'],
        'guest_max_size' => 2048,
        'disk' => 'public',
        // null: only the disk above.
        'allowed_disks' => null,
        'directory' => 'tallkit-uploads',
        // []: every known kind but code (json, js, html...).
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
        // More extensions for a kind (['image' => ['heic']]) or kinds of your own (['model' => ['stl']]).
        'file_types' => [],
        // KB, by kind (the rest take "default"), or one number for all.
        'max_size' => [
            'image' => 5120,
            'video' => 51200,
            'default' => 20480,
        ],
        // true: Livewire's upload limit becomes the largest size above (unless the app sets its own rules).
        'livewire_max_size' => true,
    ],
];

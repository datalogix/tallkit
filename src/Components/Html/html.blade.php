<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    @if ($charset) <meta charset="{{ $charset }}"> @endif
    @if ($viewport) <meta name="viewport" content="{{ $viewport }}"> @endif
    @if ($csrfToken) <meta name="csrf-token" content="{{ csrf_token() }}"> @endif
    <title>{{ $title }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style type="text/tailwindcss">
        {!! file_get_contents(__DIR__.'/../../../vendor/datalogix/tallkit/resources/css/tallkit.css') !!}
    </style>
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
    {{ $slot }}
    @tallkitScripts
</body>
</html>

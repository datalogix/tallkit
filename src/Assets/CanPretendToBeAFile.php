<?php

namespace TALLKit\Assets;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait CanPretendToBeAFile
{
    public function pretendResponseIsFile(string $file, ?string $contentType = null)
    {
        $contentType ??= $this->getContentType($file);
        $headers = ['Content-Type' => $contentType];

        if (config('app.debug')) {
            return response()->file($file, $headers);
        }

        $expires = strtotime('+1 year');
        $lastModified = filemtime($file);
        $cacheControl = 'public, max-age=31536000';

        if ($this->matchesCache($lastModified)) {
            return response()->make('', 304, [
                'Expires' => $this->httpDate($expires),
                'Cache-Control' => $cacheControl,
            ]);
        }

        $headers['Expires'] = $this->httpDate($expires);
        $headers['Cache-Control'] = $cacheControl;
        $headers['Last-Modified'] = $this->httpDate($lastModified);

        if (Str::endsWith($file, '.br')) {
            $headers['Content-Encoding'] = 'br';
        }

        return response()->file($file, $headers);
    }

    protected function matchesCache($lastModified)
    {
        $ifModifiedSince = app(Request::class)->header('if-modified-since');

        return $ifModifiedSince !== null && @strtotime($ifModifiedSince) === $lastModified;
    }

    protected function httpDate($timestamp)
    {
        return sprintf('%s GMT', gmdate('D, d M Y H:i:s', $timestamp));
    }

    protected function getContentType($file)
    {
        if (Str::endsWith($file, '.css')) {
            return 'text/css; charset=utf-8';
        }

        if (Str::endsWith($file, '.js')) {
            return 'application/javascript; charset=utf-8';
        }
    }
}

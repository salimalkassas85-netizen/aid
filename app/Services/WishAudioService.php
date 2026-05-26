<?php

namespace App\Services;

class WishAudioService
{
    public function resolve(?string $audioStyle): ?string
    {
        if (! $audioStyle || $audioStyle === 'none') {
            return null;
        }

        $path = config("wish_audio.{$audioStyle}");

        if (! $path || ! file_exists(public_path($path))) {
            return null;
        }

        return '/'.$path;
    }
}

<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class WishAudioService
{
    public function storeRecording(UploadedFile $file, string $code): ?string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'webm');

        if (! in_array($extension, ['webm', 'ogg', 'mp3', 'wav', 'm4a', 'mp4'], true)) {
            $extension = 'webm';
        }

        $directory = public_path('audio/wishes');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = $code.'.'.$extension;
        $file->move($directory, $filename);

        return '/audio/wishes/'.$filename;
    }
}

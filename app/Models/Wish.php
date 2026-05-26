<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Wish extends Model
{
    protected $fillable = [
        'sender_name',
        'receiver_name',
        'relationship',
        'style',
        'audio_style',
        'message',
        'audio_path',
        'code',
    ];

    protected static function booted(): void
    {
        static::creating(function (Wish $wish): void {
            if ($wish->code) {
                return;
            }

            do {
                $code = Str::random(10);
            } while (self::where('code', $code)->exists());

            $wish->code = $code;
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VCard extends Model
{
    protected $fillable = [
        'slug',
        'user_id',
        'session_id',
        'title',
        'bio',
        'avatar_path',
        'social_links',
    ];

    protected $casts = [
        'social_links' => 'collection',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (VCard $card) {
            if (empty($card->slug)) {
                $card->slug = Str::ulid()->toBase32();
            }
        });
    }

    public function getPublicUrlAttribute(): string
    {
        return route('vcard.show', $this->slug);
    }

    public function getQrDownloadUrlAttribute(): string
    {
        return route('vcard.qr.download', $this->slug);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SigningDocument extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'sent_at' => 'datetime',
        'signed_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function events()
    {
        return $this->hasMany(SigningDocumentEvent::class)->latest();
    }

    public function canOpen(): bool
    {
        return $this->sent_at !== null
            && $this->revoked_at === null
            && $this->signed_at === null
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}

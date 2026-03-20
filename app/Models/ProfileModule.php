<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfileModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type', // banner, avatar, bio, tip_jar, comm_slots, gallery
        'zone', // header, main, sidebar
        'order',
        'settings', // JSON configuration
    ];

    protected $casts = [
        'settings' => 'array',
        'order' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

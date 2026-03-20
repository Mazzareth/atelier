<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'page_layout',
        'modules',
        'is_default',
    ];

    protected $casts = [
        'modules' => 'array',
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

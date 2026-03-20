<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_request_id',
        'user_id',
        'message',
        'kind',
        'attachments',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(CommissionRequest::class, 'commission_request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

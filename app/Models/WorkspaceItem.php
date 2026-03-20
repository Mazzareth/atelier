<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkspaceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_request_id',
        'type',
        'title',
        'background',
        'content',
        'file_path',
        'x',
        'y',
        'width',
        'height',
        'z_index',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'x' => 'integer',
        'y' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'z_index' => 'integer',
    ];

    public function commissionRequest(): BelongsTo
    {
        return $this->belongsTo(CommissionRequest::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkspaceConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_request_id',
        'from_workspace_item_id',
        'to_workspace_item_id',
    ];

    public function commissionRequest(): BelongsTo
    {
        return $this->belongsTo(CommissionRequest::class);
    }

    public function fromItem(): BelongsTo
    {
        return $this->belongsTo(WorkspaceItem::class, 'from_workspace_item_id');
    }

    public function toItem(): BelongsTo
    {
        return $this->belongsTo(WorkspaceItem::class, 'to_workspace_item_id');
    }
}

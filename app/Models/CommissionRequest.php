<?php

namespace App\Models;

use App\Models\Concerns\TracksReadStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommissionRequest extends Model
{
    use HasFactory;
    use TracksReadStatus {
        unreadCountFor as private trackUnreadCountFor;
        markReadFor as private trackMarkReadFor;
    }

    protected $fillable = [
        'artist_id',
        'requester_id',
        'conversation_id',
        'title',
        'details',
        'budget',
        'status',
        'tracker_stage',
        'artist_response',
        'responded_at',
        'tracker_stage_updated_at',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_NEEDS_INFO = 'needs_info';

    public const TRACKER_QUEUE = 'queue';
    public const TRACKER_ACTIVE = 'active';
    public const TRACKER_DELIVERY = 'delivery';
    public const TRACKER_DONE = 'done';

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
            'tracker_stage_updated_at' => 'datetime',
            'budget' => 'decimal:2',
            'reference_images' => 'array',
        ];
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CommissionMessage::class)->latest('id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function workspaceItems(): HasMany
    {
        return $this->hasMany(WorkspaceItem::class)->orderBy('z_index')->orderBy('id');
    }

    public function workspaceConnections(): HasMany
    {
        return $this->hasMany(WorkspaceConnection::class)->orderBy('id');
    }

    public function unreadCountFor(User $user): int
    {
        if ($this->conversation) {
            return $this->conversation->unreadCountFor($user);
        }

        return $this->trackUnreadCountFor($user);
    }

    public function markReadFor(User $user): void
    {
        if ($this->conversation) {
            $this->conversation->markReadFor($user);
        }

        $this->trackMarkReadFor($user);
    }

    protected function getReadAtFor(User $user): ?Carbon
    {
        return $this->artist_id === $user->id
            ? $this->artist_last_read_at
            : $this->requester_last_read_at;
    }

    protected function setReadAtFor(User $user): void
    {
        if ($this->artist_id === $user->id) {
            $this->forceFill(['artist_last_read_at' => now()])->save();

            return;
        }

        if ($this->requester_id === $user->id) {
            $this->forceFill(['requester_last_read_at' => now()])->save();
        }
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ACCEPTED,
            self::STATUS_DECLINED,
            self::STATUS_NEEDS_INFO,
        ];
    }

    public static function trackerStageOptions(): array
    {
        return [
            self::TRACKER_QUEUE,
            self::TRACKER_ACTIVE,
            self::TRACKER_DELIVERY,
            self::TRACKER_DONE,
        ];
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isTrackable(): bool
    {
        return $this->isAccepted() && !empty($this->tracker_stage);
    }
}

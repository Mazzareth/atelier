<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'kind',
        'title',
        'user_one_last_read_at',
        'user_two_last_read_at',
    ];

    protected function casts(): array
    {
        return [
            'user_one_last_read_at' => 'datetime',
            'user_two_last_read_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class)->latest('id');
    }

    public function commissionRequest()
    {
        return $this->hasOne(CommissionRequest::class);
    }

    public function includesUser(User $user): bool
    {
        return $this->user_one_id === $user->id || $this->user_two_id === $user->id;
    }

    public function otherPartyFor(User $user): ?User
    {
        if ($this->user_one_id === $user->id) {
            return $this->userTwo;
        }

        if ($this->user_two_id === $user->id) {
            return $this->userOne;
        }

        return null;
    }

    public function unreadCountFor(User $user): int
    {
        $readAt = $this->user_one_id === $user->id
            ? $this->user_one_last_read_at
            : ($this->user_two_id === $user->id ? $this->user_two_last_read_at : null);

        return $this->messages()
            ->where('user_id', '!=', $user->id)
            ->when($readAt, fn ($query) => $query->where('created_at', '>', $readAt))
            ->count();
    }

    public function markReadFor(User $user): void
    {
        if ($this->user_one_id === $user->id) {
            $this->forceFill(['user_one_last_read_at' => now()])->save();
            return;
        }

        if ($this->user_two_id === $user->id) {
            $this->forceFill(['user_two_last_read_at' => now()])->save();
        }
    }

    public static function between(int $a, int $b)
    {
        return static::where(function ($query) use ($a, $b) {
            $query->where('user_one_id', $a)->where('user_two_id', $b);
        })->orWhere(function ($query) use ($a, $b) {
            $query->where('user_one_id', $b)->where('user_two_id', $a);
        });
    }
}

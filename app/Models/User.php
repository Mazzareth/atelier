<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Models\CommissionRequest;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'active_profile', 'page_layout', 'total_revenue', 'subscriber_count', 'commission_count'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'total_revenue' => 'decimal:2',
            'subscriber_count' => 'integer',
            'commission_count' => 'integer',
        ];
    }
    
    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }
    
    /**
     * Check if the user has artist capabilities.
     * (Admins also have artist capabilities).
     */
    public function isArtist(): bool
    {
        return $this->role === UserRole::Artist || $this->role === UserRole::Admin;
    }
    
    /**
     * Check if the artist is currently in "play" mode (commissioner) vs "work" mode.
     */
    public function isActingAsArtist(): bool
    {
        return $this->isArtist() && $this->active_profile === 'artist';
    }

    /**
     * Get the artist's customized profile modules.
     */
    public function profileModules(): HasMany
    {
        return $this->hasMany(ProfileModule::class);
    }

    public function profileConfigs(): HasMany
    {
        return $this->hasMany(ProfileConfig::class);
    }

    public function receivedCommissionRequests(): HasMany
    {
        return $this->hasMany(CommissionRequest::class, 'artist_id');
    }

    public function sentCommissionRequests(): HasMany
    {
        return $this->hasMany(CommissionRequest::class, 'requester_id');
    }
}

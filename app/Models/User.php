<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\QueuedResetPassword;
use App\Notifications\QueuedVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'email_verified_at',
        'phone_verified_at',
        'password',
        'avatar',
        'google_id',
        'currency',
        'language',
        'notification_email',
        'notification_push',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
    ];

    protected $appends = ['avatar_url', 'has_password'];

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            // Agar image hi nahi hai toh default placeholder de do
            return url('/images/default-avatar.png');
        }

        // CHECK: Agar avatar "http" se shuru ho raha hai, toh wo Google ka URL hai
        if (str_starts_with($this->avatar, 'http')) {
            return $this->avatar; // Direct Google ka URL bhej do
        }
        // CHECK: Agar sirf naam hai, toh storage ka path lagao
        return url('/storage/' . $this->avatar);
    }
    public function getHasPasswordAttribute(): bool
    {
        return !is_null($this->password);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'author_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot('role', 'joined_at');
    }

    public function deviceTokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    /**
     * Check if the user has app-level admin role.
     * Named isAppAdmin() to avoid conflict with group-level isAdmin() checks.
     */
    public function isAppAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBanned(): bool
    {
        return $this->banned_at !== null;
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new QueuedVerifyEmail);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new QueuedResetPassword($token));
    }

    /**
     * Generate a random friend code candidate, e.g. "KRM459".
     */
    public static function generateFriendCode(): string
    {
        $letters = '';
        for ($i = 0; $i < 3; $i++) {
            $letters .= chr(random_int(65, 90));
        }

        $numbers = str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);

        return $letters . $numbers;
    }

    /**
     * Every user gets a unique friend_code on creation. Generation races under
     * concurrent registrations, so retry on a unique-constraint collision
     * instead of relying on the pre-insert existence check alone.
     */
    protected function performInsert(Builder $query)
    {
        if (empty($this->friend_code)) {
            $this->friend_code = static::generateFriendCode();
        }

        $attempts = 0;

        while (true) {
            try {
                return parent::performInsert($query);
            } catch (QueryException $e) {
                $attempts++;
                if ($attempts >= 5 || !str_contains($e->getMessage(), 'friend_code')) {
                    throw $e;
                }
                $this->friend_code = static::generateFriendCode();
            }
        }
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_email' => 'boolean',
            'notification_push' => 'boolean',
            'banned_at' => 'datetime',
        ];
    }
}

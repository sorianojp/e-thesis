<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    public const ROLE_STUDENT = 'student';
    public const ROLE_ADVISER = 'adviser';
    public const ROLE_ADMIN = 'admin';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'step_token',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'step_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // 'password' => 'hashed',
            'step_token' => 'encrypted:string',
            'role' => 'string',
        ];
    }

    public function isAdmin(): bool { return $this->role === self::ROLE_ADMIN; }

    public function isAdviser(): bool { return $this->role === self::ROLE_ADVISER; }

    public function isStudent(): bool { return $this->role === self::ROLE_STUDENT; }

    public function canReviewTheses(): bool
    {
        return $this->isAdviser();
    }

    public function thesisMemberships(): BelongsToMany
    {
        return $this->belongsToMany(ThesisTitle::class, 'thesis_title_members', 'student_id', 'thesis_title_id')
            ->withTimestamps();
    }

}

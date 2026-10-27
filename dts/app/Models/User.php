<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'userID';
    public $incrementing = true; // or false if not auto-increment
    protected $keyType = 'int'; // or 'string' if your PK is not integer

    public function getRouteKeyName()
    {
        return 'userID';
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'firstName',
        'middleName',
        'lastName',
        'roleID',
        'departmentID',
        'phoneNo',
        'is_locked',
        'locked_at',
        'failed_login_attempts',
        'locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'password' => 'hashed',
        ];
    }

    /**
     * Search name, username, email and role. Every word typed has to match somewhere, in any order.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $words = preg_split('/\s+/', trim((string) $term), -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $word) {
            $like = '%' . $word . '%';
            $query->where(function ($q) use ($like) {
                $q->where('username', 'like', $like)
                  ->orWhere('firstName', 'like', $like)
                  ->orWhere('lastName', 'like', $like)
                  ->orWhere('email', 'like', $like)
                  ->orWhereHas('role', fn ($r) => $r->where('roleName', 'like', $like));
            });
        }

        return $query;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'roleID', 'roleID');
    }
}

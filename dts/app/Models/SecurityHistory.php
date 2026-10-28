<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SecurityHistory extends Model
{
    protected $primaryKey = 'historyID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'userID', 'ip', 'action', 'details',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }

    /** Record an account security event (login, lock, password reset, etc.). */
    public static function record(?User $user, string $action, ?string $details = null): void
    {
        self::create([
            'userID'  => $user?->userID,
            'ip'      => request()->ip(),
            'action'  => $action,
            'details' => $details,
        ]);
    }
}

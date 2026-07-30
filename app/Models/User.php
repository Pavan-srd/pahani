<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'mandal_id',
        'email',
        'password',
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

    public function getMandal()
    {
        return $this->belongsTo(Mandal::class, 'mandal_id');
    }
    
    /**
     * Many-to-Many relationship for assigned mandals
     * User can be assigned to multiple mandals via user_mandals pivot table
     */
    public function mandals()
    {
        return $this->belongsToMany(
            Mandal::class,
            'user_mandals',      // pivot table name
            'user_id',           // foreign key on pivot
            'mandal_id'          // related foreign key on pivot
        )->withTimestamps();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}

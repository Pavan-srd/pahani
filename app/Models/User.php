<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function workingOffice()
    {
        return $this->belongsTo(WorkingOffice::class, 'working_office_id');
    }

        /**
     * Get the user's document permissions
     */
    public function documentPermission(): HasOne
    {
        return $this->hasOne(UserDocumentPermission::class);
    }
 
    /**
     * Get or create document permission for this user
     */
    public function getOrCreateDocumentPermission()
    {
        return $this->documentPermission()->firstOrCreate(
            ['user_id' => $this->id],
            ['can_view' => false, 'can_edit' => false]
        );
    }
 
    /**
     * Check if user can view documents
     */
    public function canViewDocuments(): bool
    {
        // Admin can always view
        if ($this->role === 'admin') {
            return true;
        }
 
        return $this->getOrCreateDocumentPermission()->canView();
    }
 
    /**
     * Check if user can edit documents
     */
    public function canEditDocuments(): bool
    {
        // Admin can always edit
        if ($this->role === 'admin') {
            return true;
        }
 
        return $this->getOrCreateDocumentPermission()->canEdit();
    }
    
}

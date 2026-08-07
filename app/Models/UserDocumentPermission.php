<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDocumentPermission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_document_permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'can_view',
        'can_edit',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'can_view' => 'boolean',
        'can_edit' => 'boolean',
    ];

    /**
     * Get the user that owns the permission.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user can view documents
     */
    public function canView(): bool
    {
        return $this->can_view ?? false;
    }

    /**
     * Check if user can edit documents
     */
    public function canEdit(): bool
    {
        return $this->can_edit ?? false;
    }

    /**
     * Grant view permission
     */
    public function grantViewPermission()
    {
        $this->update(['can_view' => true]);
    }

    /**
     * Grant edit permission
     */
    public function grantEditPermission()
    {
        $this->update(['can_edit' => true]);
    }

    /**
     * Revoke view permission
     */
    public function revokeViewPermission()
    {
        $this->update(['can_view' => false]);
    }

    /**
     * Revoke edit permission
     */
    public function revokeEditPermission()
    {
        $this->update(['can_edit' => false]);
    }
}
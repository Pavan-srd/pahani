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
        'upload_mandal_ids',
        'view_mandal_ids',
        'edit_mandal_ids',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'can_view' => 'boolean',
        'can_edit' => 'boolean',
        'upload_mandal_ids' => 'array',
        'view_mandal_ids' => 'array',
        'edit_mandal_ids' => 'array',
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
     * Get upload mandal IDs as array
     */
    public function getUploadMandalIds(): array
    {
        return $this->upload_mandal_ids ?? [];
    }
 
    /**
     * Get view mandal IDs as array
     */
    public function getViewMandalIds(): array
    {
        return $this->view_mandal_ids ?? [];
    }
 
    /**
     * Get edit mandal IDs as array
     */
    public function getEditMandalIds(): array
    {
        return $this->edit_mandal_ids ?? [];
    }
 
    /**
     * Check if user can upload to specific mandal
     */
    public function canUploadToMandal(int $mandalId): bool
    {
        $mandalIds = $this->getUploadMandalIds();
        return in_array($mandalId, $mandalIds);
    }
 
    /**
     * Check if user can view specific mandal
     */
    public function canViewMandal(int $mandalId): bool
    {
        $mandalIds = $this->getViewMandalIds();
        return in_array($mandalId, $mandalIds);
    }
 
    /**
     * Check if user can edit specific mandal
     */
    public function canEditMandal(int $mandalId): bool
    {
        $mandalIds = $this->getEditMandalIds();
        return in_array($mandalId, $mandalIds);
    }
 
    /**
     * Set upload mandal IDs
     */
    public function setUploadMandalIds(array $mandalIds = [])
    {
        $this->update(['upload_mandal_ids' => array_values(array_filter($mandalIds))]);
    }
 
    /**
     * Set view mandal IDs
     */
    public function setViewMandalIds(array $mandalIds = [])
    {
        $this->update(['view_mandal_ids' => array_values(array_filter($mandalIds))]);
    }
 
    /**
     * Set edit mandal IDs
     */
    public function setEditMandalIds(array $mandalIds = [])
    {
        $this->update(['edit_mandal_ids' => array_values(array_filter($mandalIds))]);
    }
 
    /**
     * Revoke edit permission
     */
    public function revokeEditPermission()
    {
        $this->update(['can_edit' => false]);
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
}
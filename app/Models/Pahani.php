<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
 
class Pahani extends Model
{
    use SoftDeletes;
 
    protected $fillable = [
        'mandal_id',
        'village_id',
        'pahani_document_id',
        'document_name',
        'type',
        'physical_document',
        'file_name',
        'file_path',
        'file_size',
        'file_mime',
        'disk',
        'uploaded_by',
        'uploaded_ip',
    ];
 
    protected $casts = [
        'file_size' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
 
    // ── Relationships ─────────────────────────────────────────────────────────
    public function mandal(): BelongsTo
    {
        return $this->belongsTo(Mandal::class);
    }
 
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }
 
    public function pahaniDocument(): BelongsTo
    {
        return $this->belongsTo(PahaniDocument::class);
    }
 
    // ── Accessors ─────────────────────────────────────────────────────────────
 
    /** Human-readable file size: "1.23 MB" */
    public function getFileSizeHumanAttribute(): ?string
    {
        if (!$this->file_size) return null;
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow   = floor(log($this->file_size, 1024));
        return round($this->file_size / (1024 ** $pow), 2) . ' ' . $units[$pow];
    }
 
    /** Whether a file has actually been uploaded */
    public function getHasFileAttribute(): bool
    {
        return !empty($this->file_path);
    }
 
    /** Temporary R2 URL valid for 30 minutes */
    public function getTemporaryUrlAttribute(): ?string
    {
        if (!$this->file_path) return null;
        return Storage::disk($this->disk ?? 'r2')
            ->temporaryUrl($this->file_path, now()->addMinutes(30));
    }
}
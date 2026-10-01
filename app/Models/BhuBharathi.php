<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BhuBharathi extends Model
{
    use SoftDeletes;

    protected $table = 'bhu_bharathis';

    protected $fillable = [
        'mandal_id',
        'village_id',
        'module_id',
        'module',          // module name (kept in sync with modules.name)
        'application_number',
        'file_name',
        'file_path',
        'file_size',
        'file_mime',
        'disk',
        'uploaded_by',
        'uploaded_ip',
    ];

    protected $casts = [
        'mandal_id'   => 'integer',
        'village_id'  => 'integer',
        'module_id'   => 'integer',
        'file_size'   => 'integer',
        'uploaded_by' => 'integer',
    ];

    protected $appends = ['file_size_human'];

    // ── Relations ────────────────────────────────────────────────────────────
    public function mandal(): BelongsTo
    {
        return $this->belongsTo(Mandal::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Module master record. Named moduleMaster() because `module`
     * is already the column holding the module name.
     */
    public function moduleMaster(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ── Accessors ────────────────────────────────────────────────────────────
    public function getFileSizeHumanAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max((int) $this->file_size, 0);
        $pow   = min((int) floor(log($bytes, 1024)), count($units) - 1);

        return round($bytes / (1024 ** $pow), 2) . ' ' . $units[$pow];
    }
}
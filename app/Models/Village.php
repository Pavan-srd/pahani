<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
class Village extends Model
{
    protected $fillable = ['mandal_id', 'name', 'slug', 'is_active'];
 
    protected $casts = ['is_active' => 'boolean'];
 
    public function mandal(): BelongsTo
    {
        return $this->belongsTo(Mandal::class);
    }
 
    public function pahanis(): HasMany
    {
        return $this->hasMany(Pahani::class);
    }
}

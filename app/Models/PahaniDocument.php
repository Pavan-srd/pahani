<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
class PahaniDocument extends Model
{
    protected $fillable = ['value', 'label', 'type', 'description', 'is_active', 'sort_order'];
 
    protected $casts = ['is_active' => 'boolean'];
 
    public function pahanis(): HasMany
    {
        return $this->hasMany(Pahani::class);
    }
 
    public function scopeCore($query)
    {
        return $query->where('type', 'core');
    }
 
    public function scopeYearwise($query)
    {
        return $query->where('type', 'year');
    }
}

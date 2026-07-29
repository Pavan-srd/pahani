<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mandal extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];
 
    protected $casts = ['is_active' => 'boolean'];
 
    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
    }
 
    public function pahanis(): HasMany
    {
        return $this->hasMany(Pahani::class);
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_mandals',      // pivot table name
            'mandal_id',         // foreign key on pivot
            'user_id'            // related foreign key on pivot
        )->withTimestamps();
    }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WorkingOffice extends Model
{
    use Sluggable;
    protected $fillable = ['name', 'slug', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    
    public function sluggable(): array
    {
        return ['slug' => ['source' => 'name']];
    }
}
<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use HasFactory, HasUuids, SoftDeletes, Sluggable;

    protected $fillable = ['id','nama','slug','desc','ikon','status','parent_id', 'user_id'];
    protected $casts = [];
    protected $table = 'kategoris';

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'nama',
                'onUpdate' => true, // kalau title berubah, slug ikut update
            ]
        ];
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function user()
	{
		return $this->belongsTo(User::class);
	}
    public function file() 
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function gambar() 
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function getfilebyalias($alias)
	{
		return $this->file()->where('alias', $alias)->first();
	}

    public function parent()
{
    return $this->belongsTo(Kategori::class, 'parent_id');
}


}

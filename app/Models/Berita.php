<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Berita extends Model
{
    use HasFactory, HasUuids, SoftDeletes, sluggable;

    protected $fillable = ['id','nama','slug','desc','kategori','keterangan','view','status','user_id','verifikator_id'];
    protected $casts = [];
    protected $table = 'beritas';

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function verifikator()
	{
		return $this->belongsTo(User::class, 'verifikator_id');
    }
    
    public function verifikasis()
    {
        return $this->morphMany(Verifikasi::class, 'verifiable');
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
     public function notification() : object
    {
        return $this->morphOne(Notification::class, 'notifiable');
    }

   public function scopeFilter($query, array $filters) 
    {
        $query->when($filters['search'] ?? false, function($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                ->orWhere('desc', 'like', '%' . $search . '%');
            });
        });

        $query->when($filters['kategori'] ?? false, function($query, $kategori) {
            // ubah dari slug URL ke teks asli (spasi)
            $kategoriText = str_replace('-', ' ', $kategori);
            return $query->where('kategori', $kategoriText);
        });

        $query->when($filters['penulis'] ?? false, function($query, $penulis) {
            // filter berdasarkan user_id penulis
            return $query->where('user_id', $penulis);
        });
    }




}


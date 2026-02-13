<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengaturan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['id','judul','subjudul','deskripsi','alamat','telepon','email','peta','facebook','instagram','twiter','tiktok','youtube','call_center','user_id'];
    protected $casts = [];
    protected $table = 'pengaturans';

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

    public function logo() 
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function ikon() 
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function getfilebyalias($alias)
	{
		return $this->file()->where('alias', $alias)->first();
	}
}

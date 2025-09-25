<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tautan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['id','nama','desc','link','status','user_id'];
    protected $casts = [];
    protected $table = 'tautans';

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
}

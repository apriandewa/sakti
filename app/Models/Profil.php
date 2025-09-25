<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profil extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['id','nama','slug','desc','kategori','keterangan','menu','beranda','view','status','user_id'];
    protected $casts = [];
    protected $table = 'profils';

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}

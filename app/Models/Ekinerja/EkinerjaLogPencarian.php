<?php

namespace App\Models\Ekinerja;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class EkinerjaLogPencarian extends Model
{
    use HasUuids;

    const UPDATED_AT = null; // tabel log hanya butuh created_at

    protected $table = 'ekinerja_log_pencarian';

    protected $fillable = [
        'nip', 'nama_input', 'periode_id',
        'ip_address', 'user_agent', 'status', 'response_message',
    ];
}

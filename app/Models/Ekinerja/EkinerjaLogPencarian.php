<?php

namespace App\Models\Ekinerja;

use App\Traits\HasUuid; // TODO: sesuaikan namespace trait existing project
use Illuminate\Database\Eloquent\Model;

class EkinerjaLogPencarian extends Model
{
    use HasUuid;

    const UPDATED_AT = null; // tabel log hanya butuh created_at

    protected $table = 'ekinerja_log_pencarian';

    protected $fillable = [
        'nip', 'nama_input', 'periode_id',
        'ip_address', 'user_agent', 'status', 'response_message',
    ];
}

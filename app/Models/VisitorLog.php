<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VisitorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'browser',
        'os',
        'device',
        'url',
        'referer',
        'session_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | SCOPES - Statistik Kunjungan
    |--------------------------------------------------------------------------
    */

    /** Kunjungan hari ini */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /** Kunjungan bulan ini */
    public function scopeThisMonth($query)
    {
        return $query->whereYear('created_at', Carbon::now()->year)
                     ->whereMonth('created_at', Carbon::now()->month);
    }

    /** Kunjungan tahun ini */
    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', Carbon::now()->year);
    }

    /** Kunjungan tahun lalu */
    public function scopeLastYear($query)
    {
        return $query->whereYear('created_at', Carbon::now()->subYear()->year);
    }
}

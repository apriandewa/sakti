<?php

namespace App\Traits;

use App\Models\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as LogFacade;

trait Loggable
{
    // ✅ tempat aman simpan data sementara (tidak masuk DB)
    protected static $logBeforeData = [];

    public static function bootLoggable()
    {
        // CREATE
        static::created(function ($model) {
            self::saveLog($model, 'CREATE');
        });

        // UPDATE (ambil sebelum update)
        static::updating(function ($model) {
            self::$logBeforeData[$model->id] = $model->getOriginal();
        });

        static::updated(function ($model) {
            $before = self::$logBeforeData[$model->id] ?? null;

            self::saveLog($model, 'UPDATE', $before);

            // 🧹 bersihkan memory
            unset(self::$logBeforeData[$model->id]);
        });

        // DELETE
        static::deleted(function ($model) {
            self::saveLog($model, 'DELETE');
        });
    }

    protected static function saveLog($model, $action, $before = null)
    {
        try {

            $after = $model->getAttributes();

            // 🔥 hanya simpan perubahan saja
            if ($before) {
                $after = array_diff_assoc($after, $before);
            }

            // 🔥 buang field tidak penting
            unset(
                $after['password'],
                $after['remember_token'],
                $after['created_at'],
                $after['updated_at']
            );

            Log::create([
                'id'            => Str::uuid(),
                'loggable_type' => get_class($model),
                'loggable_id'   => $model->id,

                'ip'         => request()->ip(),
                'user_agent' => request()->userAgent(),

                'data' => [
                    'type'    => 'activity',
                    'action'  => $action,
                    'user_id' => Auth::id() ?? $model->user_id ?? null,
                    'model'   => class_basename($model),
                    'url'     => request()->fullUrl(),
                    'method'  => request()->method(),
                    'before'  => $before,
                    'after'   => $after,
                    'time'    => now()->toDateTimeString()
                ]
            ]);

        } catch (\Exception $e) {
            LogFacade::error('Log gagal: ' . $e->getMessage());
        }
    }
}
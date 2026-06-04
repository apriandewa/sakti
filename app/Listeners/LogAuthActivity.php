<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use App\Models\Log;
use Illuminate\Support\Str;

class LogAuthActivity
{
    public function handle($event)
    {
        $user = $event->user ?? null;

        // tentukan jenis event
        if ($event instanceof Login) {
            $action = 'LOGIN';
            $description = 'Login ke sistem';
        } elseif ($event instanceof Logout) {
            $action = 'LOGOUT';
            $description = 'Logout dari sistem';
        } elseif ($event instanceof Failed) {
            $action = 'FAILED';
            $description = 'Login gagal';
        } else {
            return;
        }

        Log::create([
            'id' => Str::uuid(),
            'loggable_type' => $user ? get_class($user) : \App\Models\User::class,
            'loggable_id'   => $user->id ?? null,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'data' => [
                'type'        => 'login',
                'action'      => $action,
                'user_id'     => $user->id ?? null,
                'description' => $description,
                'url'         => request()->fullUrl(),
                'method'      => request()->method(),
                'platform'    => 'web',
                'browser'     => request()->userAgent(),
                'time'        => now()->toDateTimeString()
            ]
        ]);
    }
}
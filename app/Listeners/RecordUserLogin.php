<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class RecordUserLogin
{
    public function handle(Login $event)
    {
        $user = $event->user;
        if (!$user || !isset($user->id)) {
            return;
        }

        $ip = null;
        $userAgent = null;
        $via = 'web';
        try {
            $req = request();
            $ip = $req?->ip();
            $userAgent = $req?->userAgent();
            if ($req && $req->is('api/mobile/*')) {
                $via = 'mobile';
            }
        } catch (\Throwable $e) {
        }

        try {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $ip,
                    'last_login_user_agent' => $userAgent,
                ]);
        } catch (\Throwable $e) {
        }

        try {
            DB::table('audits')->insert([
                'entity_type' => 'user',
                'entity_id' => $user->id,
                'action' => 'login',
                'user_id' => $user->id,
                'notes' => trim('Login via ' . $via . ($ip ? ' | IP: ' . $ip : '') . ($userAgent ? ' | UA: ' . $userAgent : '')),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
        }
    }
}

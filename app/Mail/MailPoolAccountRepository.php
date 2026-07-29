<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MailPoolAccountRepository
{
    public function claim(array $slots, array $excludedSlots = [])
    {
        $slots = array_values(array_diff(array_map('intval', $slots), $excludedSlots));

        if (empty($slots)) {
            return null;
        }

        foreach ($slots as $slot) {
            DB::table('mail_pool_accounts')->insertOrIgnore([
                'slot' => $slot,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return DB::transaction(function () use ($slots) {
            $now = now();
            $accounts = DB::table('mail_pool_accounts')
                ->whereIn('slot', $slots)
                ->lockForUpdate()
                ->get();

            $nextPosition = $accounts->max('rotation_position') + 1;
            $account = $accounts
                ->filter(function ($account) use ($now) {
                    return $account->cooldown_until === null
                        || Carbon::parse($account->cooldown_until)->lte($now);
                })
                ->sortBy(function ($account) {
                    return sprintf(
                        '%020d-%03d',
                        $account->rotation_position,
                        $account->slot
                    );
                })
                ->first();

            if (!$account) {
                return null;
            }

            DB::table('mail_pool_accounts')
                ->where('slot', $account->slot)
                ->update([
                    'rotation_position' => $nextPosition,
                    'last_used_at' => $now,
                    'updated_at' => $now,
                ]);

            return (int) $account->slot;
        });
    }

    public function markRateLimited($slot, $cooldownSeconds)
    {
        DB::table('mail_pool_accounts')
            ->where('slot', $slot)
            ->update([
                'cooldown_until' => now()->addSeconds($cooldownSeconds),
                'updated_at' => now(),
            ]);
    }

    public function markSuccessful($slot)
    {
        DB::table('mail_pool_accounts')
            ->where('slot', $slot)
            ->update([
                'cooldown_until' => null,
                'updated_at' => now(),
            ]);
    }

    public function secondsUntilAvailable(array $slots)
    {
        $next = DB::table('mail_pool_accounts')
            ->whereIn('slot', $slots)
            ->whereNotNull('cooldown_until')
            ->min('cooldown_until');

        return $next
            ? max(1, now()->diffInSeconds(Carbon::parse($next), false))
            : null;
    }
}

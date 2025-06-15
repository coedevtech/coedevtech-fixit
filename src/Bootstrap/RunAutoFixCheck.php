<?php

namespace Fixit\Bootstrap;

use Fixit\Enum\ErrorStatus;
use Fixit\Models\FixitError;

class RunAutoFixCheck
{
    public static function handle(): void
    {
        if (!app()->runningInConsole() && config('fixit.auto_fix.enabled')) {
            $key = 'fixit:auto-status-check';
            $cutoff = now()->subDays(config('fixit.auto_fix.inactivity_days_to_fix', 1));

            $query = FixitError::where('status', ErrorStatus::NOT_FIXED->value)
                ->where('last_seen_at', '<', $cutoff);

            if ($query->exists()) {
                $query->update(['status' => ErrorStatus::FIXED->value]);
            } elseif (!cache()->has($key)) {
                cache()->put(
                    $key,
                    now(),
                    now()->addMinutes(config('fixit.auto_fix.check_interval_minutes', 5))
                );
            }
        }
    }
}


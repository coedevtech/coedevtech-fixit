<?php

namespace Fixit\Bootstrap;

use Fixit\Enum\ErrorStatus;
use Fixit\Models\FixitError;

class RunAutoFixCheck
{
    /**
     * Automatically mark errors as "fixed" if they haven't occurred recently.
     *
     * This is useful for reducing noise from stale exceptions that are no longer active.
     * The logic only runs outside of the console (i.e., during HTTP requests),
     * and respects a configurable cooldown interval using the cache.
     */
    public static function handle(): void
    {
        // Only run in web context and if auto-fix is enabled in the config
        if (!app()->runningInConsole() && config('fixit.auto_fix.enabled')) {
            $key = 'fixit:auto-status-check';

            // Define the cutoff date: errors not seen since this time are considered "fixed"
            $cutoff = now()->subDays(config('fixit.auto_fix.inactivity_days_to_fix', 1));

            // Get all errors that have not been fixed and haven’t occurred recently
            $query = FixitError::where('status', ErrorStatus::NOT_FIXED->value)
                ->where('last_seen_at', '<', $cutoff);

            if ($query->exists()) {
                // Mark these stale errors as "fixed"
                $query->update(['status' => ErrorStatus::FIXED->value]);
            } 
            // Otherwise, set a cache key to throttle how often this check runs
            elseif (!cache()->has($key)) {
                cache()->put(
                    $key,
                    now(),
                    now()->addMinutes(config('fixit.auto_fix.check_interval_minutes', 5))
                );
            }
        }
    }
}

<?php

namespace Fixit\Commands;

use Illuminate\Console\Command;
use Fixit\Models\FixitError;
use Carbon\Carbon;

class PurgeOldLogs extends Command
{
    // Command signature and description for Artisan
    protected $signature = 'fixit:purge-old';
    protected $description = 'Delete FixIt logs older than configured retention days';

    /**
     * Handle the command execution.
     */
    public function handle()
    {
        // Check if the retention policy is enabled in the config
        if (!config('fixit.retention.enabled')) {
            $this->info('🛑 Retention policy is disabled.');
            return;
        }

        // Get the number of retention days from config (default to 30)
        $days = config('fixit.retention.days', 30);

        // Calculate the cutoff date
        $cutoff = Carbon::now()->subDays($days);

        // Count logs older than the cutoff date
        $count = FixitError::where('created_at', '<', $cutoff)->count();

        if ($count === 0) {
            $this->info("🧼 No logs older than $days days found.");
            return;
        }

        // Delete old logs from the database
        FixitError::where('created_at', '<', $cutoff)->delete();

        $this->info("🗑️ Purged $count logs older than $days days.");
    }
}

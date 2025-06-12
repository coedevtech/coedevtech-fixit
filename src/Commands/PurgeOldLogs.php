<?php

namespace Fixit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Fixit\Models\FixitError;
use Carbon\Carbon;

class PurgeOldLogs extends Command
{
    protected $signature = 'fixit:purge-old';
    protected $description = 'Delete FixIt logs older than configured retention days';

    public function handle()
    {
        if (!config('fixit.retention.enabled')) {
            $this->info('🛑 Retention policy is disabled.');
            return;
        }

        $days = config('fixit.retention.days', 30);
        $cutoff = Carbon::now()->subDays($days);

        $count = FixitError::where('created_at', '<', $cutoff)->count();

        if ($count === 0) {
            $this->info("🧼 No logs older than $days days found.");
            return;
        }

        FixitError::where('created_at', '<', $cutoff)->delete();

        $this->info("🗑️ Purged $count logs older than $days days.");
    }
}


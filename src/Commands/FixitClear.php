<?php

namespace Fixit\Commands;

use Fixit\Enum\ErrorStatus;
use Illuminate\Console\Command;
use Fixit\Models\FixitError;

class FixitClear extends Command
{
    protected $signature = 'fixit:clear
        {--only=fixed : Clear only fixed errors}
        {--before= : Clear errors logged before this date (Y-m-d)}';

    protected $description = 'Clear fixit error logs from the database';

    public function handle()
    {
        $query = FixitError::query();

        if ($this->option('only') === ErrorStatus::FIXED->value) {
            $query->where('status', ErrorStatus::FIXED->value);
        }

        if ($before = $this->option('before')) {
            $query->whereDate('created_at', '<', $before);
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info('No matching logs found to delete.');
            return;
        }

        if ($this->confirm("This will delete $count error(s). Continue?")) {
            $query->delete();
            $this->info("🧹 Deleted $count error(s).");
        }
    }
}



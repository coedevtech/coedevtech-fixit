<?php

namespace Fixit\Commands;

use Fixit\Enum\ErrorStatus;
use Illuminate\Console\Command;
use Fixit\Models\FixitError;

class FixitClear extends Command
{
    // Define the command signature with options for selective deletion
    protected $signature = 'fixit:clear
        {--only=fixed : Clear only fixed errors}
        {--before= : Clear errors logged before this date (Y-m-d)}';

    // Short description for `php artisan list`
    protected $description = 'Clear fixit error logs from the database';

    /**
     * Handle the command execution.
     */
    public function handle()
    {
        // Start building the query
        $query = FixitError::query();

        // If --only=fixed is passed, filter for only fixed errors
        if ($this->option('only') === ErrorStatus::FIXED->value) {
            $query->where('status', ErrorStatus::FIXED->value);
        }

        // If --before is provided, filter for logs created before the given date
        if ($before = $this->option('before')) {
            $query->whereDate('created_at', '<', $before);
        }

        // Count how many logs match the filters
        $count = $query->count();

        // Exit early if there's nothing to delete
        if ($count === 0) {
            $this->info('No matching logs found to delete.');
            return;
        }

        // Ask user for confirmation before deletion
        if ($this->confirm("This will delete $count error(s). Continue?")) {
            $query->delete();
            $this->info("🧹 Deleted $count error(s).");
        }
    }
}

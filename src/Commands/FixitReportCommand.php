<?php

namespace Fixit\Commands;

use Fixit\Enum\ErrorStatus;
use Illuminate\Console\Command;
use Fixit\Models\FixitError;

class FixitReportCommand extends Command
{
    // Define the available options for the command
    protected $signature = 'fixit:report 
                            {--status=not_fixed : Filter by error status (not_fixed/fixed)} 
                            {--fix= : ID of the error to mark as fixed} 
                            {--all : Show all errors regardless of status}';

    // Description for `php artisan list`
    protected $description = 'View and manage logged FixIt errors';

    /**
     * Execute the command logic.
     */
    public function handle()
    {
        // If --fix is provided, mark the corresponding error as fixed
        if ($this->option('fix')) {
            $this->markAsFixed($this->option('fix'));
            return;
        }

        // Begin query for listing errors
        $query = FixitError::query();

        // Filter by status unless --all is specified
        if (!$this->option('all')) {
            $query->where('status', $this->option('status'));
        }

        // Get the latest 20 matching errors
        $errors = $query->latest()->limit(20)->get();

        if ($errors->isEmpty()) {
            $this->info('✅ No errors found.');
            return;
        }

        // Display the result in a formatted table
        $this->table(
            ['ID', 'URL', 'IP', 'Status', 'Created'],
            $errors->map(fn ($e) => [
                $e->id,
                str($e->url)->limit(40),
                $e->ip,
                $e->status,
                $e->created_at->toDateTimeString(),
            ])
        );
    }

    /**
     * Mark a specific error by ID as fixed.
     */
    protected function markAsFixed($id)
    {
        $error = FixitError::find($id);

        if (!$error) {
            $this->error("❌ Error with ID $id not found.");
            return;
        }

        $error->status = ErrorStatus::FIXED->value;
        $error->save();

        $this->info("✅ Error ID $id marked as fixed.");
    }
}

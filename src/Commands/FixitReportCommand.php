<?php

namespace Fixit\Commands;

use Fixit\Enum\ErrorStatus;
use Illuminate\Console\Command;
use Fixit\Models\FixitError;

class FixitReportCommand extends Command
{
    protected $signature = 'fixit:report 
                            {--status=not_fixed : Filter by error status (not_fixed/fixed)} 
                            {--fix= : ID of the error to mark as fixed} 
                            {--all : Show all errors regardless of status}';

    protected $description = 'View and manage logged FixIt errors';

    public function handle()
    {
        // Option to mark error as fixed
        if ($this->option('fix')) {
            $this->markAsFixed($this->option('fix'));
            return;
        }

        // Fetch records
        $query = FixitError::query();

        if (!$this->option('all')) {
            $query->where('status', $this->option('status'));
        }

        $errors = $query->latest()->limit(20)->get();

        if ($errors->isEmpty()) {
            $this->info('✅ No errors found.');
            return;
        }

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

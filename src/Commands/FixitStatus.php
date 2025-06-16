<?php

namespace Fixit\Commands;

use Fixit\Enum\ErrorStatus;
use Illuminate\Console\Command;
use Fixit\Models\FixitError;

class FixitStatus extends Command
{
    // Define the command name
    protected $signature = 'fixit:status';

    // Description shown in `php artisan list`
    protected $description = 'View summary of FixIt error logs';

    /**
     * Display a summary of FixIt error logging status and settings.
     */
    public function handle()
    {
        // Gather basic metrics
        $total = FixitError::count();
        $fixed = FixitError::where('status', ErrorStatus::FIXED->value)->count();
        $last  = FixitError::latest()->first()?->created_at;

        // Display summary
        $this->info("FixIt Status\n=============");

        $this->line("Total Errors:   $total");
        $this->line("Unresolved:     " . ($total - $fixed));
        $this->line("Resolved:       $fixed");
        $this->line("Last Logged:    " . ($last ?? 'None'));

        // Display configuration information
        $this->line("Table:          " . config('fixit.logging.table'));
        $this->line("Encryption:     " . (config('fixit.encryption.enabled') ? 'Enabled' : 'Disabled'));

        // Show alert configuration
        $this->line("Alerts:         " . (
            config('fixit.notifications.send_on_error')
                ? 'Email to ' . config('fixit.notifications.email')
                : 'Disabled'
        ));
    }
}

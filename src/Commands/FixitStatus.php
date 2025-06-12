<?php

namespace Fixit\Commands;

use Illuminate\Console\Command;
use Fixit\Models\FixitError;

class FixitStatus extends Command
{
    protected $signature = 'fixit:status';
    protected $description = 'View summary of FixIt error logs';

    public function handle()
    {
        $total = FixitError::count();
        $fixed = FixitError::where('status', 'fixed')->count();
        $last  = FixitError::latest()->first()?->created_at;

        $this->info("FixIt Status\n=============");
        $this->line("Total Errors:  $total");
        $this->line("Unresolved:    " . ($total - $fixed));
        $this->line("Resolved:      $fixed");
        $this->line("Last Logged:   " . ($last ?? 'None'));
        $this->line("Table:         " . config('fixit.logging.table'));
        $this->line("Encryption:    " . (config('fixit.encryption.enabled') ? 'Enabled' : 'Disabled'));
        $this->line("Alerts:        " . (config('fixit.notifications.send_on_error') ? 'Email to ' . config('fixit.notifications.email') : 'Disabled'));
    }
}


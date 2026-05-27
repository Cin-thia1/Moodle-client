<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncPullCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull data from Moodle to update local database';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\SyncService $syncService)
    {
        $this->info('Starting sync pull from Moodle...');
        
        $summary = $syncService->pull();
        
        $this->info("Pull completed.");
        $this->table(
            ['Created', 'Updated', 'Errors'],
            [[$summary['created'], $summary['updated'], $summary['errors']]]
        );
    }
}

<?php

namespace App\Services\Sync;

use Illuminate\Support\Collection;
use App\Services\MoodleApiService;

interface SyncHandlerInterface
{
    /**
     * Set the Moodle API service.
     */
    public function setApi(MoodleApiService $api): void;

    /**
     * Pull data from Moodle to local DB.
     * 
     * @return array ['created' => int, 'updated' => int, 'errors' => int]
     */
    public function pull(): array;

    /**
     * Process specific operations from the queue to Moodle.
     * 
     * @param object $operation An operation row from sync_queue
     * @return bool True if successful, False if failed
     * @throws \Exception
     */
    public function processOperation(object $operation): bool;
}

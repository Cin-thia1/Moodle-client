<?php

namespace App\Services;

use App\Services\Sync\CategorySyncHandler;
use App\Services\Sync\CourseSyncHandler;
use App\Services\Sync\UserSyncHandler;
use App\Services\Sync\ParticipantSyncHandler;
use App\Services\Sync\SectionSyncHandler;
use App\Services\Sync\ModuleSyncHandler;
use App\Services\Sync\SubmissionSyncHandler;
use App\Services\Sync\GradeSyncHandler;
use App\Services\Sync\GenericSyncHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SyncService
{
    protected MoodleApiService $api;

    public function __construct(MoodleApiService $api)
    {
        $this->api = $api;
    }

    protected function getHandler(string $entityType)
    {
        $handler = match ($entityType) {
            'categories' => new CategorySyncHandler(),
            'courses' => new CourseSyncHandler(),
            'users' => new UserSyncHandler(),
            'participants' => new ParticipantSyncHandler(),
            'sections' => new SectionSyncHandler(),
            'modules' => new ModuleSyncHandler(),
            'submissions' => new SubmissionSyncHandler(),
            'grades' => new GradeSyncHandler(),
            'documents' => new GenericSyncHandler(\App\Models\Document::class),
            'announcements' => new GenericSyncHandler(\App\Models\Announcement::class),
            'quiz_attempts' => new GenericSyncHandler(\App\Models\QuizAttempt::class),
            default => throw new Exception("Unknown entity type for handler: $entityType"),
        };

        $handler->setApi($this->api);
        return $handler;
    }

    public function pull(): array
    {
        $summary = ['created' => 0, 'updated' => 0, 'errors' => 0];

        if (!$this->api->isOnline()) {
            Log::warning('Moodle offline, pull skipped');
            return $summary;
        }

        $handlers = [
            'categories',
            'users',
            'courses',
        ];

        \App\Observers\SyncObserver::$muteEvents = true;

        try {
            foreach ($handlers as $entityType) {
                $handler = $this->getHandler($entityType);
                $result = $handler->pull();
                $summary['created'] += $result['created'];
                $summary['updated'] += $result['updated'];
                $summary['errors'] += $result['errors'];
            }
        } finally {
            \App\Observers\SyncObserver::$muteEvents = false;
        }

        return $summary;
    }

    public function push(): array
    {
        $summary = ['processed' => 0, 'errors' => 0];

        try {
            if (!$this->api->isOnline()) {
                Log::info('Offline, push skipped');
                return $summary;
            }

            $queueIds = DB::table('sync_queue')
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->pluck('id');

            foreach ($queueIds as $id) {
                // Dispatcher le Job pour un traitement asynchrone par les workers
                \App\Jobs\ProcessSyncOperationJob::dispatch($id);
                $summary['processed']++;
            }

        } catch (Exception $e) {
            Log::error("Erreur push: {$e->getMessage()}");
            $summary['errors']++;
        }

        return $summary;
    }

    public function processSingleOperation(object $operation): void
    {
        try {
            $handler = $this->getHandler($operation->entity_type);
            $handler->processOperation($operation);

            DB::table('sync_queue')
                ->where('id', $operation->id)
                ->update([
                    'status' => 'done',
                    'processed_at' => now(),
                    'locked_at' => null,
                ]);

        } catch (Exception $e) {
            DB::table('sync_queue')
                ->where('id', $operation->id)
                ->increment('attempts');

            DB::table('sync_queue')
                ->where('id', $operation->id)
                ->update([
                    'status' => 'error',
                    'error_msg' => $e->getMessage(),
                    'locked_at' => null,
                ]);

            Log::error("Erreur push {$operation->entity_type}#{$operation->entity_id}: {$e->getMessage()}");
            throw $e; // Propager l'exception pour que le Job soit marqué comme échoué si nécessaire
        }
    }

    public function resolveConflict(string $entityType, int $entityId, string $strategy): void
    {
        $handler = $this->getHandler($entityType);
        if (method_exists($handler, 'resolveConflict')) {
            $handler->resolveConflict($entityId, $strategy);
        } else {
            if ($strategy === 'server_wins') {
                DB::table('sync_queue')
                    ->where('entity_type', $entityType)
                    ->where('entity_id', $entityId)
                    ->delete();
                
                $modelClass = $this->getModelClass($entityType);
                if ($modelClass) {
                    $modelClass::where('id', $entityId)->update(['sync_status' => 'synced', 'dirty' => 0]);
                }
            } elseif ($strategy === 'client_wins') {
                DB::table('sync_queue')
                    ->where('entity_type', $entityType)
                    ->where('entity_id', $entityId)
                    ->where('status', 'error')
                    ->update(['status' => 'pending', 'error_msg' => null, 'attempts' => 0]);
            } else {
                throw new Exception("Stratégie de résolution inconnue: $strategy");
            }
        }
    }

    protected function getModelClass(string $entityType): ?string
    {
        return match ($entityType) {
            'categories' => \App\Models\Category::class,
            'courses' => \App\Models\Course::class,
            'users' => \App\Models\User::class,
            'participants' => \App\Models\Participant::class,
            'sections' => \App\Models\Section::class,
            'modules' => \App\Models\Module::class,
            'submissions' => \App\Models\Submission::class,
            'grades' => \App\Models\Grade::class,
            'documents' => \App\Models\Document::class,
            'announcements' => \App\Models\Announcement::class,
            'quiz_attempts' => \App\Models\QuizAttempt::class,
            default => null,
        };
    }

    public function sync(): array
    {
        $pullSummary = $this->pull();
        $pushSummary = $this->push();

        return [
            'pull' => $pullSummary,
            'push' => $pushSummary,
            'status' => ($pullSummary['errors'] == 0 && $pushSummary['errors'] == 0) ? 'success' : 'warning',
            'synced_at' => now()->toDateTimeString()
        ];
    }
}

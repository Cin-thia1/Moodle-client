<?php

namespace App\Http\Controllers;

use App\Services\SyncService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * Controller pour l'interface de synchronisation.
 * Affiche le statut de la sync_queue, les conflits, et permet de relancer la sync.
 */
class SyncController extends Controller
{
    protected SyncService $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Affiche le statut global de la synchronisation.
     */
    public function status()
    {
        $stats = $this->getSyncStats();
        $pending = $this->getPendingItems();
        $recent = $this->getRecentSyncs();

        return view('sync.status', compact('stats', 'pending', 'recent'));
    }

    /**
     * Affiche la sync_queue avec filtres.
     */
    public function queue(Request $request)
    {
        $query = DB::table('sync_queue');

        // Filtrer par entité
        if ($request->has('entity_type') && $request->entity_type) {
            $query->where('entity_type', $request->entity_type);
        }

        // Filtrer par statut
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filtrer par opération
        if ($request->has('operation') && $request->operation) {
            $query->where('operation', $request->operation);
        }

        $entries = $query->orderBy('created_at', 'desc')->paginate(30);
        $stats = $this->getSyncStats();

        return view('sync.queue', compact('entries', 'stats'));
    }

    /**
     * Affiche les entités en conflit.
     */
    public function conflicts(Request $request)
    {
        $conflicts = DB::table('sync_queue')
            ->join('sync_queue as sq2', function ($join) {
                $join->on('sync_queue.entity_type', '=', 'sq2.entity_type')
                    ->on('sync_queue.entity_id', '=', 'sq2.entity_id');
            })
            ->where('sync_queue.status', 'error')
            ->distinct()
            ->select('sync_queue.*')
            ->orderBy('sync_queue.created_at', 'desc')
            ->paginate(30);

        $stats = $this->getSyncStats();

        return view('sync.conflicts', compact('conflicts', 'stats'));
    }

    /**
     * Lance la synchronisation complète.
     */
    public function sync(Request $request)
    {
        try {
            // Lancer la sync
            $result = $this->syncService->sync();

            $processed = $result['push']['processed'] ?? 0;
            $errors = $result['push']['errors'] ?? 0;

            return redirect()->back()
                ->with('success', "Synchronisation complétée: {$processed} opérations traitées, {$errors} erreurs");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', "Erreur lors de la synchronisation: {$e->getMessage()}");
        }
    }

    /**
     * Relance les opérations échouées.
     */
    public function retry(Request $request)
    {
        try {
            $retried = DB::table('sync_queue')
                ->where('status', 'error')
                ->update(['status' => 'pending']);

            return redirect()->route('sync.queue')
                ->with('success', "{$retried} opération(s) réinitialisées pour relance");
        } catch (\Exception $e) {
            return redirect()->route('sync.queue')
                ->with('error', "Erreur: {$e->getMessage()}");
        }
    }

    public function autoSync()
    {
        try {
            // Mode automatique (background) : effectue une synchronisation complète (push + pull)
            // pour garantir que les notifications sont à jour
            $result = $this->syncService->sync();
            
            return response()->json([
                'status' => 'success',
                'summary' => $result,
                'message' => 'Synchronisation automatique terminée'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => "Erreur lors de la synchronisation: {$e->getMessage()}"
            ], 500);
        }
    }

    /**
     * Vérifie la connexion au serveur Moodle
     */
    public function ping(\App\Services\MoodleApiService $moodleApi)
    {
        $hasPending = \Illuminate\Support\Facades\DB::table('sync_queue')->where('status', 'pending')->exists();
        $isOnline = $moodleApi->isOnline();
        
        $loggedOut = false;

        // Vérification silencieuse ("canari") du mot de passe
        if ($isOnline && \Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user->moodle_id && $user->moodle_token) {
                $isValid = $moodleApi->verifyUserToken($user->moodle_token);
                if (!$isValid) {
                    \Illuminate\Support\Facades\Auth::logout();
                    request()->session()->invalidate();
                    request()->session()->regenerateToken();
                    $loggedOut = true;
                }
            }
        }

        // Ne loguer que les événements significatifs (pas chaque ping de 15s)
        if ($loggedOut) {
            \Illuminate\Support\Facades\Log::warning("[Ping] Token invalidé, déconnexion forcée.");
        }
        
        return response()->json([
            'online' => $isOnline,
            'hasPending' => $hasPending,
            'loggedOut' => $loggedOut
        ]);
    }

    /**
     * Résout manuellement un conflit.
     */
    public function resolveConflict(Request $request, string $entityType, int $entityId)
    {
        try {
            $strategy = $request->input('strategy', 'server_wins');
            $this->syncService->resolveConflict($entityType, $entityId, $strategy);

            return redirect()->route('sync.conflicts')
                ->with('success', "Conflit résolu avec stratégie: {$strategy}");
        } catch (\Exception $e) {
            return redirect()->route('sync.conflicts')
                ->with('error', "Erreur: {$e->getMessage()}");
        }
    }

    /**
     * Récupère les statistiques de synchronisation.
     */
    private function getSyncStats(): array
    {
        $total = DB::table('sync_queue')->count();
        $pending = DB::table('sync_queue')->where('status', 'pending')->count();
        $synced = DB::table('sync_queue')->where('status', 'synced')->count();
        $error = DB::table('sync_queue')->where('status', 'error')->count();

        // Compter les entités locales en attente de sync
        $pendingEntities = [
            'categories' => \App\Models\Category::pending()->count(),
            'courses' => \App\Models\Course::pending()->count(),
            'sections' => \App\Models\Section::pending()->count(),
            'modules' => \App\Models\Module::pending()->count(),
            'participants' => \App\Models\Participant::pending()->count(),
            'submissions' => \App\Models\Submission::pending()->count(),
            'grades' => \App\Models\Grade::pending()->count(),
            'quiz_attempts' => \App\Models\QuizAttempt::pending()->count(),
            'documents' => \App\Models\Document::pending()->count(),
            'announcements' => \App\Models\Announcement::pending()->count(),
            'users' => \App\Models\User::pending()->count(),
        ];

        return [
            'total' => $total,
            'pending' => $pending,
            'synced' => $synced,
            'error' => $error,
            'pending_entities' => $pendingEntities,
            'total_pending_entities' => array_sum($pendingEntities),
        ];
    }

    /**
     * Récupère les éléments en attente de sync.
     */
    private function getPendingItems()
    {
        return DB::table('sync_queue')
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->limit(20)
            ->get();
    }

    /**
     * Récupère les syncs récentes réussies.
     */
    private function getRecentSyncs()
    {
        return DB::table('sync_queue')
            ->where('status', 'synced')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
}

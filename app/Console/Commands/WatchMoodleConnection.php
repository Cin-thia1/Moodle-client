<?php

namespace App\Console\Commands;

use App\Services\MoodleApiService;
use App\Services\SyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Surveille la connectivité Moodle en continu.
 * Dès que la connexion est rétablie (transition offline → online),
 * déclenche immédiatement une synchronisation complète.
 *
 * Usage:
 *   php artisan moodle:watch              # poll toutes les 30s (défaut)
 *   php artisan moodle:watch --interval=10
 *   php artisan moodle:watch --once       # un seul check, puis exit
 */
class WatchMoodleConnection extends Command
{
    protected $signature = 'moodle:watch
        {--interval=30 : Intervalle de vérification en secondes}
        {--once        : Effectuer un seul check et quitter}';

    protected $description = 'Surveille la connexion Moodle et synchronise automatiquement dès la reconnexion';

    protected MoodleApiService $api;
    protected SyncService $sync;

    /** État de connexion du dernier cycle */
    protected ?bool $previouslyOnline = null;

    public function __construct(MoodleApiService $api, SyncService $sync)
    {
        parent::__construct();
        $this->api  = $api;
        $this->sync = $sync;
    }

    public function handle(): int
    {
        $interval = max(5, (int) $this->option('interval'));
        $once     = $this->option('once');

        $this->printBanner($interval, $once);

        do {
            $this->runCycle();

            if ($once) {
                break;
            }

            $this->waitWithCountdown($interval);

        } while (true);

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------

    /**
     * Un cycle de vérification.
     */
    protected function runCycle(): void
    {
        $isOnline = $this->api->isOnline();
        $now      = now()->format('H:i:s');
        $pending  = $this->getPendingCount();

        if ($isOnline) {
            $justReconnected = ($this->previouslyOnline === false);

            if ($justReconnected) {
                // ✅ Transition offline → online : sync immédiate
                $this->newLine();
                $this->line("  <fg=green;options=bold>🟢 [{$now}] Connexion Moodle rétablie !</>");
                $this->line("  <fg=yellow>   → {$pending} opération(s) en attente, synchronisation en cours...</>");
                $this->newLine();

                $this->triggerSync();

            } elseif ($this->previouslyOnline === null) {
                // Premier cycle, déjà en ligne au démarrage
                $this->line("  <fg=green>🟢 [{$now}] Moodle en ligne</> — {$pending} opération(s) en attente");

                if ($pending > 0) {
                    $this->line("  <fg=yellow>   → Opérations en attente détectées, synchronisation en cours...</>");
                    $this->triggerSync();
                }
            } else {
                // Toujours en ligne, pas besoin de re-syncer ici (le scheduler s'en charge)
                $this->line("  <fg=green>🟢 [{$now}] Moodle en ligne</> — {$pending} opération(s) en attente");
            }

        } else {
            // Hors ligne
            if ($this->previouslyOnline === true) {
                $this->newLine();
                $this->line("  <fg=red;options=bold>🔴 [{$now}] Moodle hors ligne — mode offline activé</>");
                $this->newLine();
            } else {
                $this->line("  <fg=red>🔴 [{$now}] Moodle hors ligne</> — {$pending} opération(s) en attente");
            }
        }

        $this->previouslyOnline = $isOnline;
    }

    /**
     * Déclenche une synchronisation complète et affiche le résultat.
     */
    protected function triggerSync(): void
    {
        $startedAt = microtime(true);

        try {
            $result  = $this->sync->sync();
            $elapsed = round(microtime(true) - $startedAt, 2);

            $pullErrors  = $result['pull']['errors']    ?? 0;
            $pushErrors  = $result['push']['errors']    ?? 0;
            $pushDone    = $result['push']['processed'] ?? 0;
            $pullCreated = $result['pull']['created']   ?? 0;

            $this->line("  <fg=cyan>🔄 Synchronisation terminée en {$elapsed}s</>");
            $this->line("     Pull : {$pullCreated} cours traités, {$pullErrors} erreur(s)");
            $this->line("     Push : {$pushDone} opération(s) envoyées, {$pushErrors} erreur(s)");

            if ($pullErrors > 0 || $pushErrors > 0) {
                Log::warning('[moodle:watch] Synchronisation terminée avec erreurs', $result);
            } else {
                Log::info('[moodle:watch] Synchronisation automatique réussie', [
                    'elapsed'  => $elapsed,
                    'push_done' => $pushDone,
                ]);
            }

        } catch (\Exception $e) {
            $this->error("  ✗ Erreur de synchronisation: {$e->getMessage()}");
            Log::error('[moodle:watch] Erreur sync automatique: ' . $e->getMessage());
        }

        $this->newLine();
    }

    /**
     * Compte les opérations en attente dans la sync_queue.
     */
    protected function getPendingCount(): int
    {
        return DB::table('sync_queue')->where('status', 'pending')->count();
    }

    /**
     * Attend N secondes en affichant un compte à rebours discret.
     */
    protected function waitWithCountdown(int $seconds): void
    {
        $this->output->write("  <fg=gray>   Prochain check dans {$seconds}s</>");

        for ($i = 0; $i < $seconds; $i++) {
            sleep(1);
            // Permettre l'interruption propre par Ctrl+C (SIGINT)
        }

        // Effacer la ligne du compte à rebours
        $this->output->write("\r" . str_repeat(' ', 50) . "\r");
    }

    /**
     * Affiche la bannière de démarrage.
     */
    protected function printBanner(int $interval, bool $once): void
    {
        $this->newLine();
        $this->info('╔════════════════════════════════════════════════════╗');
        $this->info('║  Moodle Connection Watcher                         ║');
        $this->info('╚════════════════════════════════════════════════════╝');
        $this->newLine();

        if ($once) {
            $this->line('  Mode: <comment>VÉRIFICATION UNIQUE</comment>');
        } else {
            $this->line("  Mode: <comment>SURVEILLANCE CONTINUE</comment> (toutes les {$interval}s)");
            $this->line('  Appuyez sur <comment>Ctrl+C</comment> pour arrêter');
        }

        $this->newLine();
    }
}

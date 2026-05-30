<?php

namespace App\Console\Commands;

use App\Services\MoodleApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Commande de diagnostic : affiche l'état de connexion Moodle
 * et le nombre d'opérations en attente dans la sync_queue.
 *
 * Usage:
 *   php artisan moodle:ping
 */
class MoodlePingStatus extends Command
{
    protected $signature = 'moodle:ping';

    protected $description = 'Vérifie la connexion Moodle et affiche l\'état de la sync_queue';

    public function __construct(protected MoodleApiService $api)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->newLine();
        $this->info('╔════════════════════════════════════════╗');
        $this->info('║  Moodle — Diagnostic de synchronisation ║');
        $this->info('╚════════════════════════════════════════╝');
        $this->newLine();

        // ── Connectivité ─────────────────────────────────────────────────────
        $this->output->write('  Connexion Moodle ... ');
        $isOnline = $this->api->isOnline();

        if ($isOnline) {
            $this->line('<fg=green;options=bold>✓ EN LIGNE</>');
        } else {
            $this->line('<fg=red;options=bold>✗ HORS LIGNE</>');
        }

        // ── Sync queue ────────────────────────────────────────────────────────
        $this->newLine();
        $this->line('  <fg=cyan>État de la sync_queue :</> ');

        $stats = DB::table('sync_queue')
            ->selectRaw("status, COUNT(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statuses = ['pending', 'processing', 'done', 'error'];
        $rows     = [];

        foreach ($statuses as $status) {
            $count = $stats[$status] ?? 0;
            $label = match ($status) {
                'pending'    => "<fg=yellow>{$status}</>",
                'processing' => "<fg=blue>{$status}</>",
                'done'       => "<fg=green>{$status}</>",
                'error'      => "<fg=red>{$status}</>",
                default      => $status,
            };
            $rows[] = [$label, $count];
        }

        $this->table(['Statut', 'Nombre'], $rows);

        // ── Dernières erreurs ─────────────────────────────────────────────────
        $errors = DB::table('sync_queue')
            ->where('status', 'error')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['entity_type', 'entity_id', 'operation', 'error_msg', 'attempts']);

        if ($errors->isNotEmpty()) {
            $this->newLine();
            $this->line('  <fg=red>⚠ Dernières erreurs :</> ');
            $errorRows = $errors->map(fn ($e) => [
                $e->entity_type,
                $e->entity_id,
                $e->operation,
                $e->attempts,
                \Illuminate\Support\Str::limit($e->error_msg ?? '', 60),
            ])->toArray();
            $this->table(['Entité', 'ID', 'Opération', 'Tentatives', 'Erreur'], $errorRows);
        }

        // ── Conseil ───────────────────────────────────────────────────────────
        $pending = $stats['pending'] ?? 0;
        $this->newLine();

        if (!$isOnline && $pending > 0) {
            $this->line("  <fg=yellow>💡 {$pending} opération(s) en attente. Lancez <comment>moodle:watch</comment> pour synchroniser dès la reconnexion.</>");
        } elseif ($isOnline && $pending > 0) {
            $this->line("  <fg=yellow>💡 {$pending} opération(s) en attente. Lancez <comment>php artisan moodle:sync --push-only</comment> pour les envoyer.</>");
        } else {
            $this->line('  <fg=green>✓ Tout est synchronisé.</>');
        }

        $this->newLine();

        return self::SUCCESS;
    }
}

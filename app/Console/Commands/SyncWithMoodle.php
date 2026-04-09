<?php

namespace App\Console\Commands;

use App\Services\SyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncWithMoodle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moodle:sync {--force : Force sync même en offline} {--pull-only : Ne faire que le pull} {--push-only : Ne faire que le push}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise les données locales avec le serveur Moodle';

    /**
     * Execute the console command.
     */
    public function handle(SyncService $syncService): int
    {
        $this->line('');
        $this->info('╔════════════════════════════════════════════════════╗');
        $this->info('║  Synchronisation Moodle Offline-First              ║');
        $this->info('╚════════════════════════════════════════════════════╝');
        $this->line('');

        try {
            if ($this->option('pull-only')) {
                $this->line('Mode: <comment>PULL SEULEMENT</comment> (récupération données Moodle)');
                $this->line('');
                $this->line('Synchronisation en cours...');

                $result = $syncService->pull();

                $this->newLine();
                $this->displayResults('Pull', $result);

            } elseif ($this->option('push-only')) {
                $this->line('Mode: <comment>PUSH SEULEMENT</comment> (envoi à Moodle)');
                $this->line('');
                $this->line('Synchronisation en cours...');

                $result = $syncService->push();

                $this->newLine();
                $this->displayResults('Push', $result);

            } else {
                // Sync complet (pull + detect conflicts + push)
                $this->line('Mode: <comment>SYNCHRONISATION COMPLÈTE</comment>');
                $this->line('');
                $this->line('Synchronisation en cours...');

                $result = $syncService->sync();

                $this->newLine();
                $this->displayFullResults($result);
            }

            $this->newLine();
            $this->info('✓ Synchronisation réussie');
            $this->newLine();

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('✗ Erreur synchronisation: ' . $e->getMessage());
            Log::error('Commande moodle:sync échouée: ' . $e->getMessage());
            $this->newLine();

            return self::FAILURE;
        }
    }

    /**
     * Affiche les résultats du pull ou push.
     */
    protected function displayResults(string $operation, array $result): void
    {
        $this->line('');
        $this->line("<fg=cyan>{$operation} Summary:</> ");
        $this->line('  <info>✓ Créés/Traités:</info>  ' . ($result['created'] ?? $result['processed'] ?? 0));
        $this->line('  <info>✓ Mis à jour:</info>    ' . ($result['updated'] ?? 0));
        $this->line('  <fg=red>✗ Erreurs:</> ' . ($result['errors'] ?? 0));
    }

    /**
     * Affiche les résultats complets de la sync.
     */
    protected function displayFullResults(array $result): void
    {
        $this->line('');
        $this->table(
            ['Opération', 'Créés', 'Mis à jour', 'Erreurs'],
            [
                [
                    'Pull',
                    $result['pull']['created'] ?? 0,
                    $result['pull']['updated'] ?? 0,
                    $result['pull']['errors'] ?? 0,
                ],
                [
                    'Conflits détectés',
                    $result['conflicts']['conflicts'] ?? 0,
                    '-',
                    '-',
                ],
                [
                    'Push',
                    $result['push']['processed'] ?? 0,
                    '-',
                    $result['push']['errors'] ?? 0,
                ],
            ]
        );

        if (isset($result['completed_at'])) {
            $this->line('');
            $this->line("Complété à: <fg=gray>{$result['completed_at']}</>");
        }
    }
}

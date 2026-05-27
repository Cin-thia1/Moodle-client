<?php

namespace App\Services\Sync;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Exception;

class CategorySyncHandler extends BaseSyncHandler
{
    public function pull(): array
    {
        $summary = ['created' => 0, 'updated' => 0, 'errors' => 0];
        try {
            $moodleCategories = $this->api->getCategories();

            foreach ($moodleCategories as $moodleCat) {
                Category::updateOrCreate(
                    ['moodle_id' => $moodleCat['id']],
                    [
                        'name' => $moodleCat['name'] ?? '',
                        'sync_status' => 'synced',
                        'synced_at' => now(),
                        'dirty' => 0,
                    ]
                );
                $summary['updated']++; // Assuming updatedOrCreate
            }

            $this->logInfo("Pull catégories: " . count($moodleCategories) . " reçues");

        } catch (Exception $e) {
            $this->logError("Erreur pull catégories", $e);
            $summary['errors']++;
        }
        return $summary;
    }

    protected function getEntity(int $id): ?Model
    {
        return Category::find($id);
    }

    protected function pushCreate(Model $entity, array $payload): void
    {
        /** @var Category $entity */
        try {
            $result = $this->api->call('core_course_create_categories', [
                'categories[0][name]' => $entity->name,
                'categories[0][parent]' => 0,
            ]);

            if (isset($result[0]['id'])) {
                $entity->update([
                    'moodle_id' => $result[0]['id'],
                    'sync_status' => 'synced',
                    'synced_at' => now(),
                    'dirty' => 0,
                ]);
                $this->logInfo("CREATE catégorie#{$entity->id} → Moodle id:{$result[0]['id']}");
            }

        } catch (Exception $e) {
            $this->logError("Erreur CREATE catégorie#{$entity->id}", $e);
            throw $e;
        }
    }

    protected function pushUpdate(Model $entity, array $payload): void
    {
        /** @var Category $entity */
        try {
            if (!$entity->moodle_id) {
                throw new Exception("Catégorie sans moodle_id, impossible de mettre à jour");
            }

            $this->api->call('core_course_update_categories', [
                'categories[0][id]' => $entity->moodle_id,
                'categories[0][name]' => $entity->name,
            ]);

            $entity->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
                'dirty' => 0,
            ]);

            $this->logInfo("UPDATE catégorie#{$entity->id} → Moodle id:{$entity->moodle_id}");

        } catch (Exception $e) {
            $this->logError("Erreur UPDATE catégorie#{$entity->id}", $e);
            throw $e;
        }
    }
}

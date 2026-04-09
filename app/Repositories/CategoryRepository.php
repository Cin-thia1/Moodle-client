<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

/**
 * Repository pour gestion locale des catégories avec sync.
 * Chaque action insère dans sync_queue pour synchronisation future.
 */
class CategoryRepository
{
    /**
     * Crée une nouvelle catégorie localement et enqueue la sync.
     */
    public function create(array $data): Category
    {
        $category = Category::create([
            'name' => $data['name'],
            'moodle_id' => $data['moodle_id'] ?? null,
            'sync_status' => 'pending',
            'sync_action' => 'create',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de création
        DB::table('sync_queue')->insert([
            'operation' => 'CREATE',
            'entity_type' => 'categories',
            'entity_id' => $category->id,
            'payload' => json_encode(['name' => $category->name]),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return $category;
    }

    /**
     * Mets à jour une catégorie localement et enqueue la sync.
     */
    public function update(Category $category, array $data): Category
    {
        $oldValues = $category->only(['name']);

        $category->update([
            'name' => $data['name'] ?? $category->name,
            'sync_status' => 'pending',
            'sync_action' => 'update',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de mise à jour
        DB::table('sync_queue')->insert([
            'operation' => 'UPDATE',
            'entity_type' => 'categories',
            'entity_id' => $category->id,
            'payload' => json_encode([
                'name' => $category->name,
                'old_values' => $oldValues,
            ]),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return $category;
    }

    /**
     * Supprime une catégorie localement et enqueue la sync.
     */
    public function delete(Category $category): void
    {
        // Marquer pour suppression plutôt que de supprimer immédiatement
        $category->update([
            'sync_status' => 'pending',
            'sync_action' => 'delete',
            'dirty' => 1,
        ]);

        // Enqueue l'opération de suppression
        DB::table('sync_queue')->insert([
            'operation' => 'DELETE',
            'entity_type' => 'categories',
            'entity_id' => $category->id,
            'payload' => json_encode(['name' => $category->name]),
            'status' => 'pending',
            'created_at' => now(),
        ]);
    }

    /**
     * Récupère toutes les catégories avec statut de sync.
     */
    public function getAll()
    {
        return Category::orderBy('name')->get();
    }

    /**
     * Récupère une catégorie par ID.
     */
    public function getById(int $id): ?Category
    {
        return Category::find($id);
    }

    /**
     * Récupère les catégories en attente de sync.
     */
    public function getPending()
    {
        return Category::pending()->get();
    }

    /**
     * Récupère les catégories avec conflits.
     */
    public function getConflicts()
    {
        return Category::where('sync_status', 'conflict')->get();
    }
}

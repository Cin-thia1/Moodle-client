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
            'name'              => $data['name'],
            'parent_id'         => $data['parent_id'] ?? null,
            'idnumber'          => $data['idnumber'] ?? null,
            'description'       => $data['description'] ?? null,
            'descriptionformat' => $data['descriptionformat'] ?? 1,
            'moodle_id'         => $data['moodle_id'] ?? null,
            'sync_status'       => 'pending',
            'sync_action'       => 'create',
            'dirty'             => 1,
        ]);

        // Enqueue l'opération de création
        DB::table('sync_queue')->insert([
            'operation'   => 'CREATE',
            'entity_type' => 'categories',
            'entity_id'   => $category->id,
            'payload'     => json_encode([
                'name'              => $category->name,
                'parent_id'         => $category->parent_id,
                'idnumber'          => $category->idnumber,
                'description'       => $category->description,
                'descriptionformat' => $category->descriptionformat,
            ]),
            'status'     => 'pending',
            'created_at' => now(),
        ]);

        return $category;
    }

    /**
     * Mets à jour une catégorie localement et enqueue la sync.
     */
    public function update(Category $category, array $data): Category
    {
        $oldValues = $category->only(['name', 'parent_id', 'idnumber', 'description', 'descriptionformat']);

        $category->update([
            'name'              => $data['name'] ?? $category->name,
            'parent_id'         => array_key_exists('parent_id', $data) ? $data['parent_id'] : $category->parent_id,
            'idnumber'          => array_key_exists('idnumber', $data) ? $data['idnumber'] : $category->idnumber,
            'description'       => array_key_exists('description', $data) ? $data['description'] : $category->description,
            'descriptionformat' => $data['descriptionformat'] ?? $category->descriptionformat,
            'sync_status'       => 'pending',
            'sync_action'       => 'update',
            'dirty'             => 1,
        ]);

        // Enqueue l'opération de mise à jour
        DB::table('sync_queue')->insert([
            'operation'   => 'UPDATE',
            'entity_type' => 'categories',
            'entity_id'   => $category->id,
            'payload'     => json_encode([
                'name'              => $category->name,
                'parent_id'         => $category->parent_id,
                'idnumber'          => $category->idnumber,
                'description'       => $category->description,
                'descriptionformat' => $category->descriptionformat,
                'old_values'        => $oldValues,
            ]),
            'status'     => 'pending',
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
     * Récupère toutes les catégories avec relations parent/children/courses.
     */
    public function getAll()
    {
        return Category::with(['parent', 'children', 'courses'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Récupère une catégorie par ID avec ses relations.
     */
    public function getById(int $id): ?Category
    {
        return Category::with(['parent', 'children', 'courses'])->find($id);
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

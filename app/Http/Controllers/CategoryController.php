<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Afficher toutes les catégories
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    // Afficher une catégorie spécifique
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        return response()->json($category);
    }

    // Ajouter une nouvelle catégorie
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Catégorie créée avec succès', 'category' => $category], 201);
    }

    // Mettre à jour une catégorie existante
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Catégorie mise à jour avec succès', 'category' => $category]);
    }

    // Supprimer une catégorie
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Catégorie supprimée avec succès']);
    }

    // Obtenir les cours d'une catégorie
    public function getCourses($id)
    {
        $category = Category::with('cours')->find($id);

        if (!$category) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        return response()->json(['category' => $category->name, 'courses' => $category->cours]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Afficher toutes les catégories.
     */
    public function index()
    {
        $categories = $this->categoryRepository->getAll();
        return view('categories.index', compact('categories'));
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Enregistrer une nouvelle catégorie.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $category = $this->categoryRepository->create([
                'name' => $request->name,
            ]);

            return redirect()->route('categories.index')
                ->with('success', "Catégorie '{$category->name}' créée. Sera synchronisée avec Moodle.");

        } catch (\Exception $e) {
            return back()->with('error', "Erreur création catégorie: {$e->getMessage()}");
        }
    }

    /**
     * Afficher une catégorie.
     */
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    /**
     * Afficher le formulaire d'édition.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Mettre à jour une catégorie.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $this->categoryRepository->update($category, [
                'name' => $request->name,
            ]);

            return redirect()->route('categories.show', $category->id)
                ->with('success', "Catégorie mise à jour. Sera synchronisée avec Moodle.");

        } catch (\Exception $e) {
            return back()->with('error', "Erreur mise à jour: {$e->getMessage()}");
        }
    }

    /**
     * Supprimer une catégorie.
     */
    public function destroy(Category $category)
    {
        try {
            $name = $category->name;
            $this->categoryRepository->delete($category);

            return redirect()->route('categories.index')
                ->with('success', "Catégorie '{$name}' marquée pour suppression. Sera supprimée lors de la synchronisation.");

        } catch (\Exception $e) {
            return back()->with('error', "Erreur suppression: {$e->getMessage()}");
        }
    }

    /**
     * Afficher les cours d'une catégorie.
     */
    public function showCourses(Category $category)
    {
        $courses = $category->cours;
        return view('categories.courses', compact('category', 'courses'));
    }
}


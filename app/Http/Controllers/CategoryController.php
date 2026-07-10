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
     * Réservé aux administrateurs et managers.
     */
    public function create()
    {
        if (!auth()->user()->hasRole(['ROLE_ADMIN', 'ROLE_MANAGER'])) {
            abort(403, 'Seuls les administrateurs peuvent créer des catégories.');
        }
        $categories = Category::all();
        return view('categories.create', compact('categories'));
    }

    /**
     * Enregistrer une nouvelle catégorie.
     * Réservé aux administrateurs et managers.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['ROLE_ADMIN', 'ROLE_MANAGER'])) {
            abort(403, 'Seuls les administrateurs peuvent créer des catégories.');
        }
        $request->validate([
            'name'              => 'required|string|max:255',
            'parent_id'         => 'nullable|exists:categories,id',
            'idnumber'          => 'nullable|string|max:100',
            'description'       => 'nullable|string',
            'descriptionformat' => 'nullable|integer|in:0,1,2,4',
        ]);

        try {
            $category = $this->categoryRepository->create([
                'name'              => $request->name,
                'parent_id'         => $request->parent_id,
                'idnumber'          => $request->idnumber,
                'description'       => $request->description,
                'descriptionformat' => $request->descriptionformat ?? 1,
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
     * Réservé aux administrateurs et managers.
     */
    public function edit(Category $category)
    {
        if (!auth()->user()->hasRole(['ROLE_ADMIN', 'ROLE_MANAGER'])) {
            abort(403, 'Seuls les administrateurs peuvent modifier des catégories.');
        }
        $categories = Category::where('id', '!=', $category->id)->get();
        return view('categories.edit', compact('category', 'categories'));
    }

    /**
     * Mettre à jour une catégorie.
     * Réservé aux administrateurs et managers.
     */
    public function update(Request $request, Category $category)
    {
        if (!auth()->user()->hasRole(['ROLE_ADMIN', 'ROLE_MANAGER'])) {
            abort(403, 'Seuls les administrateurs peuvent modifier des catégories.');
        }
        $request->validate([
            'name'              => 'required|string|max:255',
            'parent_id'         => 'nullable|exists:categories,id',
            'idnumber'          => 'nullable|string|max:100',
            'description'       => 'nullable|string',
            'descriptionformat' => 'nullable|integer|in:0,1,2,4',
        ]);

        try {
            $this->categoryRepository->update($category, [
                'name'              => $request->name,
                'parent_id'         => $request->parent_id,
                'idnumber'          => $request->idnumber,
                'description'       => $request->description,
                'descriptionformat' => $request->descriptionformat ?? 1,
            ]);

            return redirect()->route('categories.show', $category->id)
                ->with('success', "Catégorie mise à jour. Sera synchronisée avec Moodle.");

        } catch (\Exception $e) {
            return back()->with('error', "Erreur mise à jour: {$e->getMessage()}");
        }
    }

    /**
     * Supprimer une catégorie.
     * Réservé aux administrateurs et managers.
     */
    public function destroy(Category $category)
    {
        if (!auth()->user()->hasRole(['ROLE_ADMIN', 'ROLE_MANAGER'])) {
            abort(403, 'Seuls les administrateurs peuvent supprimer des catégories.');
        }
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


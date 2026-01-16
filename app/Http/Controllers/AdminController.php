<?php
<<<<<<< HEAD
namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    public function __construct()
    {
        
    }

    // Liste des utilisateurs
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Afficher le formulaire de création
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    // Sauvegarder un nouvel utilisateur
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    // Afficher le formulaire d'édition
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    // Mettre à jour un utilisateur
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // // Mettre à jour le mot de passe si fourni
        // if ($request->filled('password')) {
        //     $request->validate([
        //         'password' => ['confirmed', Rules\Password::defaults()],
        //     ]);
        //     $user->update([
        //         'password' => Hash::make($request->password),
        //     ]);
        // }

        // Synchroniser les rôles
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    // Supprimer un utilisateur
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
=======

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\AssignmentFile;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function show(Module $module)
    {
        return view('assignments.show', compact('module'));
    }

    public function createGrade(Request $request, Module $module)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|min:0|max:100',
            'comment' => 'nullable|string',
            'submission_id' => 'required|exists:submissions,id',
        ]);

        $validated['teacher_id'] = Auth::id();

        Grade::create($validated);

        return redirect()->route('assignments.show', $module)
            ->with('success', 'Note attribuée avec succès.');
    }

    public function composeTest(Request $request, Module $module)
    {
        $validated = $request->validate([
            'selected_files' => 'required|array',
            'selected_files.*' => 'exists:assignment_files,id',
            'instructions' => 'required|string',
        ]);

        // Logique pour composer l'épreuve
        // ...

        return redirect()->route('assignments.show', $module)
            ->with('success', 'Épreuve composée avec succès.');
    }
        public function submissions($moduleId)
    {
        // Just find the module and display the submissions page
        $module = Module::findOrFail($moduleId);
        
        // Return the view with the module data
        return view('assignment-submissions', compact('module'));
>>>>>>> Evaluation
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request, MoodleUserService $moodleUserService)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    // 1) Vérifier que l'utilisateur existe sur Moodle
    $moodleUser = $moodleUserService->getUserByEmail($validated['email']);

    if (!$moodleUser) {
        return back()->withErrors([
            'email' => "Cet email n'existe pas dans Moodle. Crée d'abord le compte dans Moodle."
        ])->withInput();
    }

    // 2) Créer local + lier moodle_id + définir le nouveau mdp local
    $user = User::create([
        'moodle_id' => $moodleUser->id,
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    // (optionnel) assigner un rôle si tu veux
    // $user->assignRole('ROLE_STUDENT');

    return redirect()->route('login')->with('success', 'Compte activé. Connecte-toi.');
}


    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Get the URL to the user's profile picture.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function profilePictureUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->profilePictureUrl()
                            ? Storage::url($this->profilePictureUrl())
                            : asset('images/default-profile-picture.png'),
        );
    }
}

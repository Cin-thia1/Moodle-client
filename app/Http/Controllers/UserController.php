<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MoodleApiService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\Request;
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
    public function store(Request $request, MoodleApiService $moodleApiService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 1) Vérifier que l'utilisateur existe sur Moodle
        $moodleUsers = $moodleApiService->getUsers([
            ['key' => 'email', 'value' => $validated['email']]
        ]);
        $moodleUser = $moodleUsers[0] ?? null;

        if (!$moodleUser) {
            return back()->withErrors([
                'email' => "Cet email n'existe pas dans Moodle. Crée d'abord le compte dans Moodle."
            ])->withInput();
        }

        // 2) Créer local + lier moodle_id + définir le nouveau mdp local
        $user = User::create([
            'moodle_id' => $moodleUser['id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // model handles hashing via casts
        ]);

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

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($validated['password']) {
            $updateData['password'] = $validated['password'];
        }

        $user->update($updateData);

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
            get: fn (mixed $value) => $value
                            ? Storage::url($value)
                            : asset('images/default-profile-picture.png'),
        );
    }
}

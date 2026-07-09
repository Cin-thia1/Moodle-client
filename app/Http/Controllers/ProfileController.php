<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(): View
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information (name, email, photo).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Remplir les champs validés (name, email)
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Gestion de la photo de profil
        $hasNewPicture = false;
        if ($request->hasFile('profile_picture')) {
            $request->validate([
                'profile_picture' => 'image|max:2048',
            ]);

            // Supprimer l'ancienne photo si c'est un fichier uploadé (pas la photo par défaut)
            if (
                $user->profile_picture
                && str_starts_with($user->profile_picture, 'profile_pictures/')
                && Storage::disk('public')->exists($user->profile_picture)
            ) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Stocker la nouvelle photo — chemin relatif : profile_pictures/filename.ext
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
            $hasNewPicture = true;
        }

        $user->save();

        // Push updates to Moodle
        try {
            $moodleUserService = app(\App\Services\MoodleUserService::class);
            $moodleUserService->pushUserUpdates($user, $hasNewPicture);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur synchronisation vers Moodle depuis profil : ' . $e->getMessage());
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the Moodle API token.
     */
    public function updateToken(Request $request): RedirectResponse
    {
        $request->validate([
            'moodle_token' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $user->moodle_token = $request->moodle_token;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'token-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Supprimer la photo de profil uploadée si elle existe
        if (
            $user->profile_picture
            && str_starts_with($user->profile_picture, 'profile_pictures/')
            && Storage::disk('public')->exists($user->profile_picture)
        ) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

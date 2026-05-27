<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        
        // 1. Mise à jour locale
        // Note: Pas de Hash::make car le modèle User a le cast 'hashed'
        $user->update([
            'password' => $validated['password'],
        ]);

        // 2. Mise à jour sur Moodle (si applicable)
        if ($user->moodle_id) {
            try {
                $moodleApi = app(\App\Services\MoodleApiService::class);
                if ($moodleApi->isOnline()) {
                    $moodleApi->updateUserPassword($user->moodle_id, $validated['password']);
                    
                    // Rafraîchir le token car l'ancien est invalidé par le serveur Moodle lors du changement de mot de passe
                    $newToken = $moodleApi->authenticateUser($user->username, $validated['password']);
                    if ($newToken) {
                        $user->update(['moodle_token' => $newToken]);
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Erreur sync password Moodle: " . $e->getMessage());
                // On ne bloque pas l'utilisateur si Moodle est down, 
                // mais on pourrait ajouter un message d'avertissement.
            }
        }

        return back()->with('status', 'password-updated');
    }
}

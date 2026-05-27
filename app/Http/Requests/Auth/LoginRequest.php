<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = $this->input('login');
        $password = $this->input('password');
        $remember = $this->boolean('remember');

        // Déterminer si c'est un email ou un username
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 1. Tenter l'authentification locale classique
        if (Auth::attempt([$field => $login, 'password' => $password], $remember)) {
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // 2. Si échec local, tenter via l'API Moodle (recherche à la volée sur Moodle)
        $moodleApi = app(\App\Services\MoodleApiService::class);
        
        // Si la saisie ressemble à un email, on extrait la partie locale pour le username Moodle par défaut,
        // sinon on utilise la saisie brute comme username Moodle.
        $moodleUsername = $field === 'email' ? explode('@', $login)[0] : $login;

        // Tenter directement l'authentification sur Moodle
        $authResult = $moodleApi->authenticateUser($moodleUsername, $password);

        if ($authResult) {
            // Récupérer l'ID utilisateur Moodle et d'autres infos de base
            $siteInfo = $moodleApi->getSiteInfoByToken($authResult);
            
            if ($siteInfo) {
                $moodleId = $siteInfo['userid'];
                
                // Récupérer le profil complet (pour l'e-mail, nom complet, etc.)
                $profile = $moodleApi->getUserProfileByToken($authResult, $moodleId);
                
                $email = $profile['email'] ?? ($field === 'email' ? $login : "{$moodleUsername}@email.test");
                $firstname = $profile['firstname'] ?? $moodleUsername;
                $lastname = $profile['lastname'] ?? '';
                $fullname = $profile['fullname'] ?? "{$firstname} {$lastname}";

                // Déterminer le rôle à partir des custom fields de Moodle
                $role = 'ROLE_STUDENT'; // par défaut
                if (isset($profile['customfields']) && is_array($profile['customfields'])) {
                    foreach ($profile['customfields'] as $customField) {
                        if (isset($customField['shortname']) && $customField['shortname'] === 'client_role') {
                            if (isset($customField['value']) && $customField['value'] === 'teacher') {
                                $role = 'ROLE_TEACHER';
                            }
                            break;
                        }
                    }
                }

                // Chercher si l'utilisateur existe déjà en local
                $user = \App\Models\User::where('moodle_id', $moodleId)
                            ->orWhere('email', $email)
                            ->orWhere('username', $moodleUsername)
                            ->first();

                if (!$user) {
                    // Création à la volée !
                    $user = \App\Models\User::create([
                        'name' => $fullname,
                        'username' => $moodleUsername,
                        'email' => $email,
                        'password' => $password, // Automatiquement haché par l'attribut cast 'hashed' de User
                        'moodle_id' => $moodleId,
                        'moodle_token' => $authResult,
                        'must_change_password' => false,
                        'profile_picture' => 'images/default-profile-picture.png',
                        'sync_status' => 'synced',
                        'synced_at' => now(),
                        'dirty' => false,
                    ]);

                    // Assigner le rôle détecté (remplace d'éventuels rôles par défaut)
                    $user->syncRoles([$role]);
                    
                    \Illuminate\Support\Facades\Log::info("[Auth JIT] Nouvel utilisateur créé localement via Moodle: {$moodleUsername} (ID Moodle: {$moodleId}, Role: {$role})");
                } else {
                    // Mise à jour de l'utilisateur existant
                    $user->update([
                        'moodle_id' => $moodleId,
                        'password' => $password,
                        'moodle_token' => $authResult,
                        'must_change_password' => false,
                        'sync_status' => 'synced',
                        'synced_at' => now(),
                        'dirty' => false,
                    ]);
                    
                    // Mettre à jour le rôle
                    $user->syncRoles([$role]);
                    
                    \Illuminate\Support\Facades\Log::info("[Auth JIT] Utilisateur local synchronisé via Moodle: {$moodleUsername} (ID Moodle: {$moodleId}, Role: {$role})");
                }

                Auth::login($user, $remember);

                RateLimiter::clear($this->throttleKey());
                return;
            }
        }

        // 3. Échec total
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('login')).'|'.$this->ip());
    }
}

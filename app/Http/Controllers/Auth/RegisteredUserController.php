<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Services\MoodleUserService;

class RegisteredUserController extends Controller
{
    protected $moodleUserService;

    public function __construct(MoodleUserService $moodleUserService)
    {
        $this->moodleUserService = $moodleUserService;
    }
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
  public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'role'     => ['nullable', 'in:ROLE_STUDENT,ROLE_TEACHER'],
    ]);

    try {
        // 1) Chercher l'utilisateur dans Moodle (si dispo)
        $moodleUser = $this->moodleUserService->getUserByEmail($request->email);

        // 2) Préparer les données user
        $userData = [
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'profile_picture' => 'images/default-profile-picture.png',
        ];

        if ($moodleUser) {
            $userData['moodle_id'] = $moodleUser->id;
            $userData['name'] = $moodleUser->fullname ?? $userData['name'];

            Log::info('Utilisateur Moodle lié avec succès', [
                'email'     => $request->email,
                'moodle_id' => $moodleUser->id,
                'fullname'  => $moodleUser->fullname ?? 'N/A',
            ]);
        } else {
            Log::info('Aucun utilisateur Moodle trouvé pour cet email', [
                'email' => $request->email,
            ]);
        }

        // 3) Créer l'utilisateur local
        $user = User::create($userData);

        // 4) Déterminer le rôle : priorité au choix du formulaire
        $role = $request->role ?: 'ROLE_STUDENT';

        // Si pas de choix et user Moodle trouvé -> auto-déduction
        if (!$request->role && $moodleUser) {
            $isTeacher = $this->moodleUserService->isUserTeacherInAnyCourse($moodleUser->id);
            $role = $isTeacher ? 'ROLE_TEACHER' : 'ROLE_STUDENT';
        }

        // 5) Assigner le rôle (une seule fois)
        $user->syncRoles([$role]);

        // 6) Fin montréale
        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));

    } catch (\Exception $e) {
    Log::error("Erreur lors de l'inscription avec Moodle", [
        'email' => $request->email,
        'error' => $e->getMessage(),
    ]);

    // Récupérer l'user déjà créé ou le créer si pas encore fait
    $user = User::where('email', $request->email)->first();
    
    if (!$user) {
        $user = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'profile_picture' => 'images/default-profile-picture.png',
        ]);
    }

    // Priorité au choix du formulaire, sinon student
    $role = $request->role ?: 'ROLE_STUDENT';
    $user->syncRoles([$role]);

    event(new Registered($user));
    Auth::login($user);

    return redirect(route('dashboard'))
        ->with('warning', 'Inscription réussie, mais connexion Moodle indisponible.');
}
}

}
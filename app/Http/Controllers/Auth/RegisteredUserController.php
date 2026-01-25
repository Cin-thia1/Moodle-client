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
        'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'role'     => ['nullable', 'in:ROLE_STUDENT,ROLE_TEACHER'],
    ]);

    try {
        $moodleUser = $this->moodleUserService->getUserByEmail($request->email);

        $userData = [
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'profile_picture'  => 'images/default-profile-picture.png',
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
            Log::info('Aucun utilisateur Moodle trouvé pour cet email', ['email' => $request->email]);
        }

        // Création de l'utilisateur LOCAL
        $user = User::create($userData);

        // Gestion des rôles
        $role = $request->filled('role') ? $request->role : 'ROLE_STUDENT';

        // Optionnel : si on veut prioriser un rôle basé sur Moodle (ex: teacher si déjà teacher dans un cours)
        // Attention : ici on utilise $moodleUser->id (pas $user->id) car $user vient juste d'être créé
        if ($moodleUser) {
            $isTeacherInMoodle = Course::where('teacher_id', $moodleUser->id)->exists();
            if ($isTeacherInMoodle) {
                $role = 'ROLE_TEACHER';
            }
        }

        $user->assignRole($role);   // ou syncRoles() si tu veux remplacer

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    } catch (\Exception $e) {
        Log::error('Erreur lors de l\'inscription avec Moodle', [
            'email' => $request->email,
            'error' => $e->getMessage(),
        ]);

        // Fallback : création locale sans lien Moodle
        $user = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'profile_picture' => 'images/default-profile-picture.png',
        ]);

        $user->assignRole($request->role ?? 'ROLE_STUDENT');

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard'))
            ->with('warning', 'Inscription réussie, mais connexion Moodle indisponible. La synchronisation se fera plus tard.');
    }
}

}

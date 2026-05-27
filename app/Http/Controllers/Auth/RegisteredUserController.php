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
    public function create(): RedirectResponse
    {
        return redirect()->route('login')->with('warning', 'L\'inscription est gérée par l\'administrateur Moodle.');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('login');
    }

}

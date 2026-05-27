<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $categories = Category::all();

        if ($user->hasRole(['ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_MANAGER'])) {
            $courses = Course::all();
            $assignments = Module::where('modname', 'assign')
                ->where('duedate', '>=', now())
                ->with(['section.course'])
                ->orderBy('duedate', 'asc')
                ->get();
        } else {
            $courses = $user->courses()->get();
            $courseIds = $courses->pluck('id');
            $assignments = Module::where('modname', 'assign')
                ->where('duedate', '>=', now())
                ->whereHas('section', function ($q) use ($courseIds) {
                    $q->whereIn('course_id', $courseIds);
                })
                ->with(['section.course'])
                ->orderBy('duedate', 'asc')
                ->get();
        }

        return view('dashboard', compact('courses', 'categories', 'assignments'));
    }
}

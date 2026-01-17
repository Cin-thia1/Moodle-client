<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * FRONT ONLY : Sidebar cours + devoirs (mock)
     */
    public function index(Request $request)
    {
        // Mock cours
        $courses = collect([
            (object)['id' => 1, 'fullname' => 'Mathématiques'],
            (object)['id' => 2, 'fullname' => 'Informatique'],
            (object)['id' => 3, 'fullname' => 'Physique'],
        ]);

        $selectedCourseId = (int)($request->query('course_id', $courses->first()->id));

        // Mock devoirs
        $assignments = collect([
            (object)['id' => 10, 'course_id' => 1, 'name' => 'Devoir 1 - Algèbre', 'due_date' => '2026-01-20 23:59', 'max_grade' => 20],
            (object)['id' => 11, 'course_id' => 1, 'name' => 'Devoir 2 - Fonctions', 'due_date' => '2026-01-27 23:59', 'max_grade' => 20],
            (object)['id' => 12, 'course_id' => 2, 'name' => 'TP Laravel', 'due_date' => '2026-01-25 23:59', 'max_grade' => 20],
        ])->where('course_id', $selectedCourseId)->values();

        return view('assignments.index', compact('courses', 'selectedCourseId', 'assignments'));
    }

    /**
     * FRONT ONLY : Détail devoir + tableau élèves (mock)
     */
    public function show($id)
    {
        // Mock cours
        $courses = collect([
            (object)['id' => 1, 'fullname' => 'Mathématiques'],
            (object)['id' => 2, 'fullname' => 'Informatique'],
            (object)['id' => 3, 'fullname' => 'Physique'],
        ]);

        // Mock devoir courant
        $module = (object)[
            'id' => (int)$id,
            'course_id' => 1,
            'name' => 'Devoir 1 - Algèbre',
            'description' => 'Résoudre les exercices 1 à 5. Joindre un PDF.',
            'due_date' => '2026-01-20 23:59',
            'max_grade' => 20,
        ];

        // Mock liste devoirs du cours (menu constant)
        $assignments = collect([
            (object)['id' => 10, 'course_id' => 1, 'name' => 'Devoir 1 - Algèbre'],
            (object)['id' => 11, 'course_id' => 1, 'name' => 'Devoir 2 - Fonctions'],
        ]);

        // Mock étudiants du cours
        $students = collect([
            (object)['id' => 101, 'name' => 'Alice N.', 'email' => 'alice@test.com'],
            (object)['id' => 102, 'name' => 'Bruno K.', 'email' => 'bruno@test.com'],
            (object)['id' => 103, 'name' => 'Carla P.', 'email' => 'carla@test.com'],
        ]);

        // Mock soumissions indexées par student_id
        $subByUser = collect([
            101 => (object)['status' => 'submitted', 'file' => 'devoir_alice.pdf', 'comment' => 'Voici mon devoir', 'grade' => 16],
            102 => null,
            103 => (object)['status' => 'graded', 'file' => 'devoir_carla.pdf', 'comment' => null, 'grade' => 18],
        ]);

        return view('assignments.show', compact('courses', 'assignments', 'module', 'students', 'subByUser'));
    }

    /**
     * FRONT ONLY : Carnet de notes (mock)
     */
    public function gradebook($courseId)
    {
        $course = (object)['id' => (int)$courseId, 'fullname' => 'Mathématiques'];

        $assignments = collect([
            (object)['id' => 10, 'name' => 'Devoir 1'],
            (object)['id' => 11, 'name' => 'Devoir 2'],
        ]);

        $students = collect([
            (object)['id' => 101, 'name' => 'Alice N.'],
            (object)['id' => 102, 'name' => 'Bruno K.'],
            (object)['id' => 103, 'name' => 'Carla P.'],
        ]);

        $matrix = [
            101 => [10 => 16, 11 => 14],
            102 => [10 => null, 11 => 12],
            103 => [10 => 18, 11 => 19],
        ];

        return view('assignments.gradebook', compact('course', 'assignments', 'students', 'matrix'));
    }
}

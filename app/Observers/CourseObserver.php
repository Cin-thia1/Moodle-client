<?php

namespace App\Observers;

use App\Models\Course;

class CourseObserver
{
    public function created(Course $course): void
    {
        // Géré par CourseRepository
    }

    public function updated(Course $course): void
    {
        // Géré par CourseRepository
    }

    public function deleting(Course $course): void
    {
        // Géré par CourseRepository
    }
}
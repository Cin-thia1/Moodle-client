<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Services\MoodleParticipantService;
use Illuminate\Console\Command;

class SyncCourseUserTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:course-user {course_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize participants table with course_user table for consistency';

    /**
     * Execute the console command.
     */
    public function handle(MoodleParticipantService $participantService)
    {
        if ($courseId = $this->argument('course_id')) {
            // Synchronise un cours spécifique
            $course = Course::findOrFail($courseId);
            $synced = $participantService->syncAllToCourseUserTable($courseId);
            $this->info("✓ Synchronized $synced participants for course: {$course->fullname}");
        } else {
            // Synchronise tous les cours
            $courses = Course::all();
            $totalSynced = 0;

            foreach ($courses as $course) {
                $synced = $participantService->syncAllToCourseUserTable($course->id);
                $totalSynced += $synced;
                $this->line("  • {$course->fullname}: $synced participants");
            }

            $this->info("✓ Synchronized $totalSynced total participants across all courses");
        }
    }
}

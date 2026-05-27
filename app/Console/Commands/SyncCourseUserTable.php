<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Participant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
    public function handle()
    {
        $courseId = $this->argument('course_id');

        $query = Participant::where('status', 1);
        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        $participants = $query->get();
        $fixed = 0;

        foreach ($participants as $p) {
            $exists = DB::table('course_user')
                ->where('user_id', $p->user_id)
                ->where('course_id', $p->course_id)
                ->exists();

            if (!$exists) {
                try {
                    DB::table('course_user')->insert([
                        'user_id'    => $p->user_id,
                        'course_id'  => $p->course_id,
                        'created_at' => $p->enrolled_at ?? now(),
                        'updated_at' => now(),
                    ]);
                    $fixed++;
                    $this->line("  • Fixed: User #{$p->user_id} → Course #{$p->course_id}");
                } catch (\Exception $e) {
                    $this->warn("  ⚠ Skip: User #{$p->user_id} → Course #{$p->course_id}: " . $e->getMessage());
                }
            }
        }

        $this->info("✓ Done. Fixed {$fixed} missing enrollments.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\Document;
use App\Models\Announcement;
use App\Models\NotificationRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /**
     * Fetch user notifications (context-aware: filters by course if viewing one).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $courseId = $request->query('course_id');
        $notifications = [];

        // 1. Get user's authorized course IDs based on role
        $courseIds = [];
        if ($user->hasRole('ROLE_TEACHER')) {
            $courseIds = Course::where('teacher_id', $user->id)->pluck('id')->toArray();
        } elseif ($user->hasRole('ROLE_STUDENT')) {
            $courseIds = $user->courses->pluck('id')->toArray();
        } else {
            $courseIds = Course::pluck('id')->toArray(); // Admin can see all
        }

        // Apply dynamic context filtering
        if ($courseId) {
            $courseId = (int) $courseId;
            if (in_array($courseId, $courseIds) || $user->hasRole('ROLE_ADMIN')) {
                $filterCourseIds = [$courseId];
            } else {
                $filterCourseIds = []; // No access to this course
            }
        } else {
            $filterCourseIds = $courseIds;
        }

        if (empty($filterCourseIds)) {
            return response()->json([
                'notifications' => [],
                'unread_count' => 0,
            ]);
        }

        // 2. Fetch notifications from all 5 sources

        // Source 1: Welcome message when a student joins a new course (enrollment)
        if ($user->hasRole('ROLE_STUDENT')) {
            $enrollments = DB::table('course_user')
                ->where('user_id', $user->id)
                ->whereIn('course_id', $filterCourseIds)
                ->get();

            foreach ($enrollments as $enrollment) {
                $course = Course::find($enrollment->course_id);
                if (!$course) continue;

                $notifications[] = [
                    'key' => 'enrollment_' . $enrollment->course_id,
                    'type' => 'welcome',
                    'title' => 'Bienvenue dans ' . $course->fullname,
                    'message' => 'Vous avez rejoint le cours ' . $course->fullname . ' avec succès.',
                    'date' => $enrollment->created_at ? \Illuminate\Support\Carbon::parse($enrollment->created_at)->toIso8601String() : now()->subDays(2)->toIso8601String(),
                    'url' => route('courses.show', $enrollment->course_id),
                    'course_name' => $course->fullname,
                ];
            }
        }

        // Source 2 & 3: New assignment / New quiz posted (Modules)
        $modules = Module::whereIn('modname', ['assign', 'quiz'])
            ->whereHas('section', function ($q) use ($filterCourseIds) {
                $q->whereIn('course_id', $filterCourseIds);
            })
            ->with('section.course')
            ->get();

        foreach ($modules as $module) {
            $course = $module->section->course ?? null;
            if (!$course) continue;

            if ($module->modname === 'assign') {
                $dueDateStr = $module->duedate ? $module->duedate->format('d/m/Y H:i') : 'Pas de date limite';
                $notifications[] = [
                    'key' => 'assign_' . $module->id,
                    'type' => 'assignment',
                    'title' => 'Nouveau devoir : ' . $module->name,
                    'message' => 'Un nouveau devoir a été publié dans ' . $course->shortname . '. Date limite : ' . $dueDateStr . '.',
                    'date' => $module->created_at ? $module->created_at->toIso8601String() : now()->subDays(1)->toIso8601String(),
                    'url' => route('assignments.show', $module->id),
                    'course_name' => $course->fullname,
                ];
            } elseif ($module->modname === 'quiz') {
                $closeDateStr = $module->timeclose ? $module->timeclose->format('d/m/Y H:i') : 'Pas de date limite';
                $notifications[] = [
                    'key' => 'quiz_' . $module->id,
                    'type' => 'quiz',
                    'title' => 'Nouveau quiz : ' . $module->name,
                    'message' => 'Un nouveau quiz est disponible dans ' . $course->shortname . '. Date de fermeture : ' . $closeDateStr . '.',
                    'date' => $module->created_at ? $module->created_at->toIso8601String() : now()->subDays(1)->toIso8601String(),
                    'url' => route('quiz.show', $module->id),
                    'course_name' => $course->fullname,
                ];
            }
        }

        // Source 4: New course resource / material uploaded (Documents)
        $documents = Document::whereIn('course_id', $filterCourseIds)
            ->where('status', 1)
            ->with('course')
            ->get();

        foreach ($documents as $document) {
            $course = $document->course;
            if (!$course) continue;

            $notifications[] = [
                'key' => 'document_' . $document->id,
                'type' => 'document',
                'title' => 'Nouveau document : ' . $document->filename,
                'message' => 'Une nouvelle ressource a été ajoutée au cours ' . $course->shortname . '.',
                'date' => $document->created_at ? $document->created_at->toIso8601String() : ($document->file_date ? $document->file_date->toIso8601String() : now()->subDays(1)->toIso8601String()),
                'url' => route('documents.preview', $document->id),
                'course_name' => $course->fullname,
            ];
        }

        // Source 5: Important announcements from the teacher (Announcements)
        $announcements = Announcement::whereIn('course_id', $filterCourseIds)
            ->where('status', 1)
            ->with('course')
            ->get();

        foreach ($announcements as $announcement) {
            $course = $announcement->course;
            if (!$course) continue;

            $notifications[] = [
                'key' => 'announcement_' . $announcement->id,
                'type' => 'announcement',
                'title' => 'Annonce : ' . $announcement->subject,
                'message' => Str::limit(strip_tags($announcement->message), 100),
                'date' => $announcement->published_at ? $announcement->published_at->toIso8601String() : ($announcement->created_at ? $announcement->created_at->toIso8601String() : now()->subDays(1)->toIso8601String()),
                'url' => route('announcements.index', $announcement->course_id),
                'course_name' => $course->fullname,
            ];
        }

        // 3. Resolve read status by checking with database
        $readKeys = NotificationRead::where('user_id', $user->id)
            ->pluck('notification_key')
            ->toArray();

        foreach ($notifications as &$n) {
            $n['read'] = in_array($n['key'], $readKeys);
        }
        unset($n); // Break reference

        // 4. Sort notifications by date descending
        usort($notifications, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // 5. Limit results to latest 15 to keep dropdown highly performant
        $limitedNotifications = array_slice($notifications, 0, 15);

        // 6. Calculate total unread count (based on all dynamic ones, not just limited list, or from limited list)
        $unreadCount = collect($notifications)->where('read', false)->count();

        return response()->json([
            'notifications' => $limitedNotifications,
            'unread_count' => $unreadCount,
            'context_course_id' => $courseId,
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'key' => 'required|string',
        ]);

        NotificationRead::firstOrCreate([
            'user_id' => $user->id,
            'notification_key' => $validated['key'],
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all listed notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'keys' => 'required|array',
            'keys.*' => 'required|string',
        ]);

        foreach ($validated['keys'] as $key) {
            NotificationRead::firstOrCreate([
                'user_id' => $user->id,
                'notification_key' => $key,
            ]);
        }

        return response()->json(['success' => true]);
    }
}

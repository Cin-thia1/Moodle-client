<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Spatie\Permission\Middleware\PermissionMiddleware as MiddlewarePermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware as MiddlewareRoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware as MiddlewareRoleOrPermissionMiddleware;
use Spatie\Permission\Middlewares\RoleMiddleware;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Spatie\Permission\Middlewares\RoleOrPermissionMiddleware;
use App\Models\Course;
use App\Observers\CourseObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router): void
    {
        // Enregistrer les observers
        $modelsToObserve = [
            \App\Models\Course::class,
            \App\Models\Section::class,
            \App\Models\Module::class,
            \App\Models\Category::class,
            \App\Models\Participant::class,
            \App\Models\User::class,
            \App\Models\Document::class,
            \App\Models\Announcement::class,
            \App\Models\QuizAttempt::class,
            \App\Models\Submission::class,
            \App\Models\Grade::class,
        ];

        foreach ($modelsToObserve as $model) {
            $model::observe(\App\Observers\SyncObserver::class);
        }
        
        $router->aliasMiddleware('role', MiddlewareRoleMiddleware::class);
        $router->aliasMiddleware('permission', MiddlewarePermissionMiddleware::class);
        $router->aliasMiddleware('role_or_permission', MiddlewareRoleOrPermissionMiddleware::class);
    }
}

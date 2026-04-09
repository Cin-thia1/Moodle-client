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
        Course::observe(CourseObserver::class);
        
        $router->aliasMiddleware('role', MiddlewareRoleMiddleware::class);
        $router->aliasMiddleware('permission', MiddlewarePermissionMiddleware::class);
        $router->aliasMiddleware('role_or_permission', MiddlewareRoleOrPermissionMiddleware::class);
    }
}

<?php

namespace Tests\Unit;

use App\Services\MoodleApiService;
use Tests\TestCase;

class MoodleApiServiceTest extends TestCase
{
    public function test_uses_admin_token_for_category_write_operations_when_configured(): void
    {
        config()->set('moodle.api_token', 'user-token');
        config()->set('moodle.api_admin_token', 'admin-token');

        $service = new MoodleApiService();
        $method = new \ReflectionMethod(MoodleApiService::class, 'resolveToken');
        $method->setAccessible(true);

        $this->assertSame('admin-token', $method->invoke($service, 'core_course_create_categories'));
        $this->assertSame('admin-token', $method->invoke($service, 'core_course_update_categories'));
        $this->assertSame('admin-token', $method->invoke($service, 'core_course_delete_categories'));
    }

    public function test_falls_back_to_regular_token_for_non_category_operations(): void
    {
        config()->set('moodle.api_token', 'user-token');
        config()->set('moodle.api_admin_token', null);

        $service = new MoodleApiService();
        $method = new \ReflectionMethod(MoodleApiService::class, 'resolveToken');
        $method->setAccessible(true);

        $this->assertSame('user-token', $method->invoke($service, 'core_webservice_get_site_info'));
    }
}

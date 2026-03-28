<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Controllers\LandingPageController;
use App\Core\Router;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class CaptureRouteTest extends TestCase
{
    public function testCaptureRouteIsRegistered(): void
    {
        $router = new Router();
        require base_path('routes/web.php');

        $reflection = new ReflectionMethod($router, 'resolveRoute');

        [$action, $params] = $reflection->invoke($router, 'GET', '/capture/estimation-bordeaux.php');

        $this->assertNotNull($action, 'The /capture/{slug}.php route should be registered');
        $this->assertSame(LandingPageController::class, $action[0]);
        $this->assertSame('capture', $action[1]);
        $this->assertSame(['estimation-bordeaux.php'], $params);
    }

    public function testCaptureControllerMethodExists(): void
    {
        $this->assertTrue(method_exists(LandingPageController::class, 'capture'));
    }
}

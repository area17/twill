<?php

namespace A17\Twill\Tests\Unit\Helpers;

use A17\Twill\Facades\TwillRoutes;
use A17\Twill\Tests\Unit\TestCase;

class RouteHelpersTest extends TestCase
{
    public function testGetAuthRedirectPath()
    {
        config(['twill.auth_login_redirect_path' => null, 'twill.admin_app_url' => null, 'twill.admin_app_path' => null]);

        $this->assertEquals('/admin', TwillRoutes::getAuthRedirectPath());

        config(['twill.admin_app_path' => 'twill']);

        $this->assertEquals('/twill', TwillRoutes::getAuthRedirectPath());

        config(['twill.admin_app_url' => 'https://admin.example.com/']);

        $this->assertEquals('https://admin.example.com/twill', TwillRoutes::getAuthRedirectPath());

        config(['twill.auth_login_redirect_path' => '/redirect']);

        $this->assertEquals('/redirect', TwillRoutes::getAuthRedirectPath());
    }
}

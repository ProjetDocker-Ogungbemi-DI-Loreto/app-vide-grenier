<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\Home;

class HomeControllerTest extends TestCase
{
    public function testHomePageReturnsContent()
    {
        $homeController = new Home();
        $result = $homeController->index(); // Assuming index() returns some view or content
        $this->assertNotEmpty($result);
    }
}

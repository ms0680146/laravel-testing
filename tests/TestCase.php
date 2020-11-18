<?php

namespace Tests;

use Mockery;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function initMock($class)
    {
        // Mockery::mock 可以利用 Reflection 機制幫我們建立假物件
        $mock = Mockery::mock($class);
        // Service Container 的 instance 方法可以讓我們
        // 用假物件取代原來的物件
        $this->app->instance($class, $mock);

        return $mock;
    }
}

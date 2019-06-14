<?php

/*
 * This file is part of the godruoyi/laravel-tencent007-captcha.
 *
 * (c) Godruoyi <godruoyi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return ['Godruoyi\Tencent007\ServiceProvider'];
    }
}

<?php

/*
 * This file is part of the godruoyi/laravel-tencent007-captcha.
 *
 * (c) Godruoyi <godruoyi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests;

use Godruoyi\Tencent007\Response;

class ResponseTest extends TestCase
{
    public function createResponseInstance($result = null)
    {
        return new Response($result);
    }

    public function testInit()
    {
        $this->assertInstanceOf(Response::class, $this->createResponseInstance());
    }

    public function testSuccess()
    {
        $this->assertTrue($this->createResponseInstance(['response' => 1])->success());
        $this->assertFalse($this->createResponseInstance(['response' => 2])->success());
    }

    public function testMessage()
    {
        $this->assertSame($this->createResponseInstance(['err_msg' => 'err_msg'])->message(), 'err_msg');
        $this->assertNotSame($this->createResponseInstance(['err_msg' => 'err_msg2'])->message(), 'err_msg');
    }

    public function testGetOriginal()
    {
        $this->assertSame($this->createResponseInstance('a')->getOriginal(), 'a');
        $this->assertArrayHasKey('a', $this->createResponseInstance(['a' => 'test'])->getOriginal());
    }

    public function testLevel()
    {
        $this->assertSame(
            $this->createResponseInstance([
                'evil_level' => 70, 'response' => 1,
            ])->level(),
            70
        );

        $this->assertNotSame(
            $this->createResponseInstance([
                'evil_level' => 70, 'response' => 0,
            ])->level(),
            70
        );

        $this->assertSame(
            $this->createResponseInstance([
                'evil_level' => 70, 'response' => 0,
            ])->level(false),
            70
        );
    }
}

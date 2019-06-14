<?php

/*
 * This file is part of the godruoyi/laravel-tencent007-captcha.
 *
 * (c) Godruoyi <godruoyi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests;

use Godruoyi\Tencent007\Client;

class ClientTest extends TestCase
{
    public function testInitialize()
    {
        $client = new Client('appid', 'secretkey');

        $this->assertTrue($client instanceof Client);
    }

    public function testDefaultParamter()
    {
        $this->assertIsArray($config = $this->app->get('config')->get('007'));

        $config['appid'] = 'appid';
        $config['secret'] = 'secret';

        $this->app->get('config')->set(['007' => $config]);
        $client = new Client();

        $this->assertTrue($client instanceof Client);
    }

    public function testGetHttpClient()
    {
        $this->assertInstanceOf('GuzzleHttp\Client', (new Client('a', 'b'))->getHttpClient());
    }

    public function testException()
    {
        $this->expectException(\RuntimeException::class);
        $client = new Client();
    }

    public function testCheck()
    {
        $this->expectException(\RuntimeException::class);

        Client::check('a', 'b');
    }

    public function testTicketVerify()
    {
        $client = new Client('appid', 'secret');

        $response = $client->ticketVerify('ticket', 'randstr', 'ip');

        $this->assertInstanceOf('Godruoyi\Tencent007\Response', $response);
    }
}

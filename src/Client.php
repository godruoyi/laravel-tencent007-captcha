<?php

/*
 * This file is part of the godruoyi/laravel-tencent007-captcha.
 *
 * (c) Godruoyi <godruoyi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Godruoyi\Tencent007;

use GuzzleHttp\Client as HttpClient;
use RuntimeException;

class Client
{
    const API_ADDRESS = 'https://ssl.captcha.qq.com/ticket/verify';

    /**
     * @var string
     */
    protected $appid;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * Register appid and secretkey.
     *
     * @param string $appid
     * @param string $secretKey
     *
     * @throws \RuntimeException
     */
    public function __construct($appid = null, $secretKey = null)
    {
        $this->appid = $appid ?: config('007.appid');
        $this->secretKey = $secretKey ?: config('007.secret');

        if (!$this->appid || !$this->secretKey) {
            throw new RuntimeException('Please configure default appid/secret for tencent 007 in config/007.php file.');
        }
    }

    /**
     * Get http client instance.
     *
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        return new HttpClient();
    }

    /**
     * Static method call.
     *
     * @param string $ticket
     * @param string $randstr
     * @param string $ip
     *
     * @return mixed
     */
    public static function check($ticket, $randstr, $ip = null)
    {
        return (new static())->ticketVerify($ticket, $randstr, $ip);
    }

    /**
     * Verify request ticket.
     *
     * @param string $ticket
     * @param string $randstr
     * @param string $ip
     *
     * @return \Godruoyi\Tencent007\Response
     */
    public function ticketVerify($ticket, $randstr, $ip = null)
    {
        $response = $this->getHttpClient()->get(self::API_ADDRESS, [
            'query' => [
                'aid' => $this->appid,
                'AppSecretKey' => $this->secretKey,
                'Ticket' => $ticket,
                'Randstr' => $randstr,
                'UserIP' => $ip ?: request()->ip(),
            ],
        ]);

        $response = (string) $response->getBody();

        $json = json_decode($response, true);

        return new Response($json);
    }
}

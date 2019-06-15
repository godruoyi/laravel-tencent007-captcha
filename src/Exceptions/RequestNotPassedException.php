<?php

/*
 * This file is part of the godruoyi/laravel-tencent007-captcha.
 *
 * (c) Godruoyi <godruoyi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Godruoyi\Tencent007\Exceptions;

use Godruoyi\Tencent007\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RequestNotPassedException extends HttpException
{
    /**
     * Result for tencent validate response.
     *
     * @var \Godruoyi\Tencent007\Response
     */
    protected $result;

    public function __construct(Response $response)
    {
        $this->result = $response;

        parent::__construct(403, 'Tencent 007 validate failure.');
    }

    /**
     * Get response.
     *
     * @return \Godruoyi\Tencent007\Response
     */
    public function getResult()
    {
        return $this->result;
    }
}

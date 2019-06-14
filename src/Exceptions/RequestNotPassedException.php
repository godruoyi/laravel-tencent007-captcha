<?php

/*
 * This file is part of the godruoyi/laravel-tencent007-captcha.
 *
 * (c) Godruoyi <godruoyi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Godruoyi\Tencent007\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RequestNotPassedException extends HttpException
{
    public function __construct($statusCode = 400, $message = 'Tencent 007 validate failure.')
    {
        parent::__construct($statusCode, $message);
    }
}

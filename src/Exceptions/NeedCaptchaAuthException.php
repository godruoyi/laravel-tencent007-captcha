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

class NeedCaptchaAuthException extends HttpException
{
    public function __construct($statusCode = 429, $message = 'Need tencent captcha certification.')
    {
        parent::__construct($statusCode, $message);
    }
}

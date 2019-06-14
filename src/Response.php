<?php

/*
 * This file is part of the godruoyi/laravel-tencent007-captcha.
 *
 * (c) Godruoyi <godruoyi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Godruoyi\Tencent007;

use Illuminate\Support\Arr;

class Response
{
    /**
     * The original value.
     *
     * @var mixed
     */
    protected $original;

    /**
     * Default level.
     *
     * @var int
     */
    protected $level = 0;

    /**
     * Default error message.
     *
     * @var string
     */
    protected $msg = 'Request failed';

    /**
     * Request has verfiy success.
     *
     * @var bool
     */
    protected $success = false;

    /**
     * Initialize Instance.
     *
     * @param mixed $result
     */
    public function __construct($result)
    {
        $this->parseResult($this->original = $result);
    }

    /**
     * Has success.
     *
     * @return bool
     */
    public function success()
    {
        return (bool) $this->success;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function message()
    {
        return (string) $this->msg;
    }

    /**
     * Get original value.
     *
     * @return mixed
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Get level.
     *
     * @return int
     */
    public function level($force = true)
    {
        if ($force) {
            return $this->success() ? $this->level : 0;
        }

        return $this->level;
    }

    /**
     * Parse response.
     *
     * @param array|null $result
     */
    protected function parseResult($result)
    {
        // use default setting
        if (!is_array($result)) {
            return;
        }

        $this->success = (1 === (int) Arr::get($result, 'response'));
        $this->level = (int) Arr::get($result, 'evil_level', '');
        $this->msg = (string) Arr::get($result, 'err_msg');
    }
}

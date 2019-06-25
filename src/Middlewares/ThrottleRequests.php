<?php

/*
 * This file is part of the godruoyi/laravel-tencent007-captcha.
 *
 * (c) Godruoyi <godruoyi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Godruoyi\Tencent007\Middlewares;

use Closure;
use Godruoyi\Tencent007\Client;
use Godruoyi\Tencent007\Exceptions\NeedCaptchaAuthException;
use Godruoyi\Tencent007\Exceptions\RequestNotPassedException;
use Godruoyi\Tencent007\Response as Tencent007Response;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Routing\Middleware\ThrottleRequests as BaseThrottleRequests;

class ThrottleRequests extends BaseThrottleRequests
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param int|string               $maxAttempts
     * @param float|int                $decayMinutes
     *
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->existsInCacheForRequest($key, $request)) {
            return $next($request);
        }

        $maxAttempts = $this->resolveMaxAttempts($request, $maxAttempts);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts, $decayMinutes)
            && ($response = $this->processTooManyAttempts($request))) {
            return $response;
        }

        $this->hit($key, $decayMinutes);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    /**
     * Process too many request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response|mixed|null
     */
    protected function processTooManyAttempts($request)
    {
        $ticket = $request->get(config('007.request_key_map.ticket', 'ticket'));
        $randstr = $request->get(config('007.request_key_map.randstr', 'randstr'));

        if (empty($ticket) || empty($randstr)) {
            return $this->buildNeedAuthException();
        }

        $checkResponse = Client::check($ticket, $randstr, $request->ip());

        if ($checkResponse->level() >= config('007.level', 70)) {
            return $this->buildNotPassedResponse($checkResponse);
        }

        config('007.cache') > 0 && $this->joinKeyToCache($key, config('007.cache'));
    }

    /**
     * Exists in cache for current request.
     *
     * @param string                   $key
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function existsInCacheForRequest($key, $request)
    {
        return config('007.cache') > 0 && app(Cache::class)->has($key.':passed');
    }

    /**
     * Rewrite hit for subclass cover.
     *
     * @param string $key
     * @param int    $decayMinutes
     */
    protected function hit($key, $decayMinutes)
    {
        return $this->limiter->hit($key, $decayMinutes);
    }

    /**
     * Build a Invalid Argument Exception.
     *
     * @return mixed
     */
    protected function buildNeedAuthException()
    {
        throw new NeedCaptchaAuthException();
    }

    /**
     * Build response from not passed.
     *
     * @param Tencent007Response $response
     *
     * @return mixed
     */
    protected function buildNotPassedResponse(Tencent007Response $response)
    {
        throw new RequestNotPassedException($response);
    }

    /**
     * @param string $key
     * @param int    $hour
     */
    protected function joinKeyToCache($key, $hour)
    {
        $added = app(Cache::class)->add($key.':passed', 1, $decayMinutes = ($hour * 60));

        if (!$added) {
            app(Cache::class)->put($key.':passed', 1, $decayMinutes);
        }
    }
}

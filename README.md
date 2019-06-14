<h1 align="center"> laravel-tencent007-captcha </h1>

<p align="center">
    <a href="https://github.styleci.io/repos/191917595"><img src="https://github.styleci.io/repos/191917595/shield?branch=master" alt="StyleCI"></a>
    <a href="https://scrutinizer-ci.com/g/godruoyi/laravel-tencent007-captcha/?branch=master"><img src="https://scrutinizer-ci.com/g/godruoyi/laravel-tencent007-captcha/badges/quality-score.png?b=master" alt="Scrutinizer Code Quality"></a>
    <a href="https://packagist.org/packages/godruoyi/laravel-tencent007-captcha"><img src="https://poser.pugx.org/godruoyi/laravel-tencent007-captcha/v/stable" alt="tencent captcha"></a>
    <a href="https://packagist.org/packages/godruoyi/laravel-tencent007-captcha"><img src="https://poser.pugx.org/godruoyi/laravel-tencent007-captcha/downloads" alt="tencent captcha"></a>
    <a href="https://packagist.org/packages/godruoyi/laravel-tencent007-captcha"><img src="https://poser.pugx.org/godruoyi/laravel-tencent007-captcha/license" alt="tencent captcha"></a>
</p>

## 安装

```shell
$ composer require godruoyi/laravel-tencent007-captcha -vvv
```

## 使用

1. 发布腾讯云防水墙配置文件。

```php
php artisan vendor:publish --provider="Godruoyi\Tencent007\ServiceProvider"
```

2. 修改应用根目录下的 `config/007.php` 中对应的参数。

## 接入

在使用 SDK 前，最好到 [腾讯云防水墙](https://007.qq.com/) 了解验证码接入的基本流程。

### 客户端接入

1. 在 Head 标签中加入以下代码，引入验证 JS 文件（建议直接在 html 中引入）：

```html
<script src="https://ssl.captcha.qq.com/TCaptcha.js"></script>
```

2. 在你想要激活验证码的 DOM 元素（eg. button、div、span）内加入以下 id 及属性：

```html
<!--点击此元素会自动激活验证码-->
<!--id : 元素的id(必须)-->
<!--data-appid : AppID(必须)-->
<!--data-cbfn : 回调函数名(必须)-->
<!--data-biz-state : 业务自定义透传参数(可选)-->
<button id="TencentCaptcha"
    data-appid="2021537529"
    data-cbfn="callback"
>验证</button>
```

3. 为验证码创建回调函数，注意函数名要与 data-cbfn 相同：

```html
window.callback = function(res){
    console.log(res)
    // res（用户主动关闭验证码）= {ret: 2, ticket: null}
    // res（验证成功） = {ret: 0, ticket: "String", randstr: "String"}
    if(res.ret === 0){
        alert(res.ticket)   // 票据
        // 将这里的 ticket randstr 一起随接口提交给后端 API
    }
}
```

完成以上操作后，点击激活验证码的元素，即可弹出验证码。

> 以上客户端接入文档来自腾讯防水墙，更多相关配置请前往 [腾讯云防水墙](https://007.qq.com/)

### 服务端使用

你可以通过客户端提供的 ticketVerify 方法来快速验证用户。

```php
class UserRegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $response = app('007')->ticketVerify($request->ticket, $request->randstr);

        if (!$response->success()) {
            // 不可信用户
        }
    }
}
```

它将返回一个类型为 `Godruoyi\Tencent007\Response` 响应，并提供了一些有用的方法。

```php
// bool 是否验证成功
$response->success();

// string 验证错误信息
$response->message();

// array|null 请求返回的原始数据
$response->getOriginal();

// int 用户恶意等级
$response->level();
```

我们还提供了一个请求频率限制的中间件 `ThrottleRequests`，它继承了 Laravel 默认的 ThrottleRequests 中间件，
可以用它来快速实现 `一分钟请求超过60次出现滑块验证` 的效果。

> 为了使用该中间件，你需要在 `app/Http/Kernel.php` 中添加配置。

```php
// app/Http/Kernel.php

protected $routeMiddleware = [
    // ...
    'throttle.007' => \Godruoyi\Tencent007\Middlewares\ThrottleRequests::class,
];
```

然后在你的路由文件中使用该中间件即可。

> throttle.007:60,1 ———— 1 分钟请求超过 60 次，出现滑块验证

```php
Route::post('users/register', 'UserRegisterController')->middleware('throttle.007:60,1');
```

## License

MIT
<?php

namespace Alp\Api\JWT\Middleware;

use Closure;

/* *
 * Laravel 5.2 中使用
 *
 * 在App\Http\Kernel里的$routeMiddleware中注册即可使用基于JWT的认证中间件
 *
 * */

class JWTAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( ! jwt()->check()) {
            return data()->sendErr(['token' => '未登录或未授权的请求。'], 401);
        }

        return $next($request);
    }
}

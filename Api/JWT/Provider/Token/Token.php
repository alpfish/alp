<?php
namespace Alp\Api\JWT\Provider\Token;

use Alp\Api\JWT\Src\JWT;
use Alp\Api\JWT\Provider\Claims\Claims;

class Token
{
    /* *
     * 创建一个JWT TOKEN
     *
     * @param  array|string $claimsKey
     * @param  mixed $claimsValue
     * @return string $token
     * */
    public static function create($claimsKey, $claimsValue=null) {

        $claims = Claims::set($claimsKey, $claimsValue);// 权利声明

        $key = config('jwt.key');// 密钥

        $alg = config('jwt.alg') ? config('jwt.alg') : 'HS256';// 算法

        $token = JWT::encode($claims, $key, $alg); // 编码

        return $token;
    }

    /* *
     * 从HTTP请求中查找TOKEN
     *
     * @return string|null
     * */
    public static function find() {

        if ($token = request()->header('token', null))
            return $token;

        if ($token = request('token', null))
            return $token;

        return null;
    }

    /* *
     * 轻触更新TOKEN并将新TOKEN返回到响应数据包中
     *
     * @param  string $token 默认为HTTP请求的token
     * @return string|null 更新返回token，未更新返回null
     * */
    public static function touch($token=null) {

        // 获取claims
        if (null !== ($claims = Claims::get($token))) {

            // touch时间检测 (exp已在JWT基类中检测)
            if (isset($claims->iat) && config('jwt.touch') < time() - $claims->iat) {
                // 更新发行和过期时间(覆盖设置才有效)
                $claims->iat = time();
                $claims->exp = config('jwt.claims.exp') ? config('jwt.claims.exp') : time() + 7*24*60*60;
                // 更新+响应+返回 token
                return data()->set('token', self::create(get_object_vars($claims)))->get('token');
            }
        }

        return null; //未更新
    }

}
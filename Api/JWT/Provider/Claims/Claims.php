<?php
namespace Alp\Api\JWT\Provider\Claims;

use Auth;
use Alp\Api\JWT\Src\JWT;
use Alp\Api\JWT\Provider\Token\Token;

class Claims
{
    // Claims 权利声明数组
    protected static $claims;

    /* *
     * 为创建JWT设置Claims数组
     *
     * @param  array|string $key
     * @param  string $value
     * @return array $claims
     * */
    public static function set($key=null, $value=null)
    {
        //发行人
        self::$claims['iss'] = config('jwt.claims.iss') ? config('jwt.claims.iss') : request()->url();
        //收听人
        if(config('jwt.claims.aud')) self::$claims['aud'] = config('jwt.claims.aud');
        //尝试设置uid
        if(auth()->user()) self::$claims['uid'] = auth()->user()->id;
        //发行时间
        self::$claims['iat'] = time();
        //过期时间
        self::$claims['exp'] = config('jwt.claims.exp') ? config('jwt.claims.exp') : time() + 7*24*60*60;
        //标题
        if(config('jwt.claims.sub')) self::$claims['sub'] = config('jwt.claims.sub');
        //not before
        if(config('jwt.claims.nbf')) self::$claims['nbf'] = config('jwt.claims.nbf');
        //JWT ID
        if(config('jwt.claims.jti')) self::$claims['jti'] = config('jwt.claims.jti');

        //设置参数
        if (is_array($key)) {
            foreach($key as $k=>$v)
                self::$claims[$k] = $v;
        }
        elseif (is_string($key) && isset($value)) {
            self::$claims[$key] = $value;
        }

        return self::$claims;
    }

    /* *
     * 从JWT中得到Claims数据
     *
     * @param  string $token
     * @return object|null $claims
     * */
    public static function get($token=null) {

        $token = is_null($token) ? Token::find() : $token;

        $key = config('jwt.key');// 密钥

        $alg = config('jwt.alg') ? config('jwt.alg') : 'HS256';// 算法

        if ($token) {
            try {
                if ($claims = JWT::decode($token, $key, array($alg))) {
                    return $claims;
                }
            } catch (\InvalidArgumentException $e) {
                data()->setErr('token', '服务器设置错误: '.$e->getMessage())->status(500);
            } catch (\Exception $e) {
                data()->setErr('token', '无效的TOKEN: '.$e->getMessage())->status(403);
            }
        }

        return null;
    }










}
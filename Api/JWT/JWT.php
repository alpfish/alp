<?php
namespace Alp\Api\JWT;

use Alp\Api\JWT\Provider\Token\Token;
use Alp\Api\JWT\Provider\Claims\Claims;
use Alp\Api\JWT\Contracts\JWT as JWTContract;

class JWT implements JWTContract
{
    protected static $jwt;

    /* *
     * 单例模式
     *
     * 便于IDE工具中使用帮助函数jwt()显示相关方法
     * 如帮助函数已绑定实例，则无需在ApiServiceProvider中再注册实例到app容器
     * */
    private function __construct() {

    }

    // 返回JWT实例
    public static function getInstance() {
        if (self::$jwt)
            return self::$jwt;
        else
            return self::$jwt = new self();
    }

    /* *
     * JWT初始化(包括认证和自动刷新TOKEN)
     *
     * 在ApiServiceProvider的boot()中引导
     *
     * @return void
     * */
    public static function init() {

        // 一次性认证
        self::auth();

        // 检查刷新token并添加到响应数据
        self::touch();
    }

    /* *
     * 与Laravel Auth进行一次性认证连接
     *
     * 使得JWT和整个应用都能共享请求token中的认证
     *
     * @return boolean
     * */
    public static function auth() {

        // 获取JWT尝试一次性认证
        if ($claims = Claims::get()) {
            if (isset($claims->uid) && is_int($claims->uid))
                auth()->onceUsingId($claims->uid);
            return true;
        }

        return false;
    }

    /* *
     * 获取JWT认证用户
     *
     * @return object|null user
     * */
    public static function user() {
        return auth()->user();
    }

    /* *
     * 检查是否认证
     *
     * @return boolean
     * */
    public static function check() {
        return auth()->check();
    }

    /* *
     * 新建JWT Token
     *
     * @param  string|array $claimsKey 权利声明数据键名（或数组时不用设置键值）
     * @param  string @$claimsValue 权利声明数据键值
     * @return string $token
     * */
    public static function token($claimsKey, $claimsValue = null) {
        return Token::create($claimsKey, $claimsValue);
    }

    /* *
     * 智能刷新
     *
     * @param  string $token 默认从请求中获取
     * @return string|null $new_token
     * */
    public static function touch($token = null) {
        return Token::touch($token);
    }

    /* *
     * 获取JWT Token中的声明数据
     *
     * @param  string $token  默认从请求中获取
     * @return object $claims 权利声明数据对象
     * */
    public static function claims($token = null) {
        return Claims::get($token);
    }



}
<?php
namespace Alp\Api\JWT\Contracts;

interface JWT
{
    // 单例模式
    public static function getInstance();

    /* *
     * JWT初始化(包括认证和自动刷新TOKEN)
     *
     * 在ApiServiceProvider的boot()中引导
     *
     * @return void
     * */
    public static function init();

    /* *
     * 与Laravel Auth进行一次性认证连接
     *
     * 使得JWT和整个应用都能共享请求token中的认证
     *
     * @return boolean
     * */
    public static function auth();

    /* *
     * 获取JWT认证用户
     *
     * @return object|null
     * */
    public static function user();

    /* *
     * 检查是否认证
     *
     * @return boolean
     * */
    public static function check();

    /* *
     * 新建JWT Token
     *
     * @param  string|array $claimsKey 权利声明数据键名（或数组时不用设置键值）
     * @param  string @$claimsValue 权利声明数据键值
     * @return string $token
     * */
    public static function token($claimsKey, $claimsValue = null);

    /* *
     * 智能刷新
     *
     * @param  string $token 默认从请求中获取
     * @return string|null $new_token
     * */
    public static function touch($token = null);

    /* *
     * 获取JWT Token中的声明数据
     *
     * @param  string $token  默认从请求中获取
     * @return object $claims 权利声明数据对象
     * */
    public static function claims($token = null);

}
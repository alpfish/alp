<?php
namespace Alp\Auth\Contracts;

interface Auth
{
    /* *
     * 用户注册
     *
     * 自动设置错误信息到data()
     *
     * @param  string $type mobile|email|social
     *
     * @Request  int $code 短信验证码(手机注册时)
     * @Request  string $mobile|$email
     * @Request  string $name
     * @Request  string $password
     * @Request  string $password_confirmation
     *
     * @return mixed User|null
     * */
    public static function register($type);

    /* *
     * 基本认证
     *
     * @Request  string $username   mobile|email|name
     * @Request  string $password
     *
     * @return object|null User|null(如有错误信息,封装在data()中)
     * */
    public static function basic();

    /* *
     * JWT认证
     *
     * @Request  string $username   mobile|email|name
     * @Request  string $password
     *
     * @return string|null $token|null(如有错误信息,封装在data()中)
     * */
    public static function jwt();

    /* *
     * 社交认证
     *
     * @return object|null User|null
     * */
    public static function social();

}
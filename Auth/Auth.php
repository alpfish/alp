<?php
namespace Alp\Auth;

use Alp\Auth\Contracts\Auth as AuthContract;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class Auth implements AuthContract
{
    /* *
     * 用户注册
     *
     * 自动设置错误信息到data()
     *
     * @param  string $type mobile|email
     *
     * @Request  int $code 短信验证码(手机注册时)
     * @Request  string $mobile|$email
     * @Request  string $name
     * @Request  string $password
     * @Request  string $password_confirmation
     *
     * @return mixed User|null
     * */
    public static function register($type)
    {
        switch($type) {
            case 'mobile' :
                return Providers\Register::withMobile();
            case 'email' :
                return Providers\Register::withEmail();
        }

        throw new InvalidParameterException('使用Auth::register()未设置正确的$type字段');
    }

    /* *
     * 基本认证
     *
     * 进行的是session登录,
     * 自动设置错误信息到data()
     *
     * @Request  string $username   mobile|email|name
     * @Request  string $password
     *
     * @return object|null User|null
     * */
    public static function basic()
    {
        return Providers\BasicAuth::login();
    }

    /* *
     * JWT认证
     *
     * 进行的是一次性登录并返回token
     * 自动设置错误信息到data()
     *
     * @Request  string $username   会员名/手机号/邮箱
     * @Request  string $password
     *
     * @return mixed string|null $token
     * */
    public static function jwt()
    {
        return Providers\JWTAuth::login();
    }

    /* *
     * 社交认证
     *
     * @return object|null User|null
     * */
    public static function social()
    {

    }
}
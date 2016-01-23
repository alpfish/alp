<?php
namespace Alp\Auth\Providers;

use Auth;
use Validator;

class JWTAuth
{
    /* *
     * JWT认证
     *
     * 进行的是一次性登录并返回token
     *
     * @Request  string $username   会员名/手机号/邮箱
     * @Request  string $password
     *
     * @return mixed string|null $token (如有错误信息,封装在data()中)
     * */
    public static function login()
    {
        // 验证请求
        $validator =  Validator::make(request()->all(), [
            'username'  => 'required',
            'password' => 'required',
        ], $messages = [
            'username.required' => '没有填写会员名/手机号/邮箱。',
            'password.required' => '没有填写密码。',

        ]);

        if ($validator->fails()){
            // 转化错误为数组并追加到data()
            data()->setErr(collect($validator->errors())->toArray());
            return null;
        }

        // 认证属性
        $loginAttributes = [
            self::loginUsername(request('username')) => request('username'),
            'password' => request('password'),
            'status' => 1,
        ];

        // 成功认证
        if (auth()->once($loginAttributes))
            return jwt()->token('uid', auth()->user()->id) ;

        data()->setErr('username', '会员名或密码不正确。'); //错误格式前后一致
        return null;
    }


    /* *
     * 获取用户字段名
     *
     * @param  string $username
     * @return string
     * */
    private static function loginUsername($username)
    {
        $preg_mobile = '/^1[34578][0-9]{9}$/';
        if (!!preg_match($preg_mobile, $username))
            return 'mobile';

        $preg_email = '/^(\w{1,25})@(\w{1,16})(\.(\w{1,4})){1,3}$/';
        if (!!preg_match($preg_email, $username))
            return 'email';

        return 'name';
    }
}
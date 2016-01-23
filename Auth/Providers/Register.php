<?php
namespace Alp\Auth\Providers;

use Alp\Common\Sms\Sms;
use Validator;

/* *
 * 依赖关系
 *
 * 类：Alp\Common\Sms\Sms;
 * 帮助函数：data() model()
 *
 * */
class Register
{
    /* *
     * 使用手机号注册
     *
     * 自动设置错误信息到data()
     *
     * @Request  int $code
     * @Request  string $mobile
     * @Request  string $name
     * @Request  string $password
     * @Request  string $password_confirmation
     *
     * @return object|null User|null
     * */
    public static function withMobile()
    {
        // 验证请求
        $validator =  Validator::make(request()->all(), [
                'code'  => 'required|numeric',
                'mobile'  => 'required|regex:/^1[34578][0-9]{9}$/|unique:users',
                'name'  => 'required|min:2|max:255|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u|unique:users',
                'password' => 'required|confirmed|min:6', // confirmed: password = password_confirmation
            ], $messages = [
                'required' => '字段不存在。',
                // TODO 本地化错误信息
            ]);

        if ($validator->fails()){
            // 转化错误为数组并追加到data()
            data()->setErr(collect($validator->errors())->toArray());
            return null;
        }

        // 验证短信校验码
        if ( ! Sms::check(request()->mobile, request()->code))
            return null;

        // 创建用户
        $user = model('User')->create([
            'name' => request()->name,
            'mobile' => request()->mobile,
            'password' => request()->password
        ]);
        // 登录 这里只能用id认证，因password已Hash，Auth::login() 和 Auth::attempt()认证需要未Hash的password
        auth()->loginUsingId($user->id);

        return auth()->user();
    }

    /* *
     * 使用手机号注册
     *
     * 自动设置错误信息到data()
     *
     * @Request  string $email
     * @Request  string $name
     * @Request  string $password
     *
     * @return object|null User|null
     * */
    public static function withEmail()
    {
        // 验证请求
        $validator =  Validator::make(request()->all(), [
            'email' => 'required|email|max:255|unique:users',
            'name'  => 'required|min:2|max:255|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u|unique:users',
            'password' => 'required|confirmed|min:6', // confirmed: password = password_confirmation
        ], $messages = [
            'required' => '字段不存在。',
            // TODO 本地化错误信息
        ]);

        if ($validator->fails()){
            // 转化错误为数组并追加到data()
            data()->setErr(collect($validator->errors())->toArray());
            return null;
        }

        // 创建用户
        $user = model('User')->create([
            'name' => request()->name,
            'email' => request()->email,
            'password' => request()->password
        ]);

        auth()->loginUsingId($user->id);

        return auth()->user();
    }
}
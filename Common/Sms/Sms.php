<?php
namespace Alp\Common\Sms;

use Alp\Common\Sms\Contracts\Sms as SmsContract;

class Sms implements SmsContract
{
    // 合并配置文件
    private static function init()
    {
        $path = realpath(__DIR__.'/config/sms.php');
        $config = app('config')->get('sms', []);

        app('config')->set('sms', array_merge(require $path, $config));
    }

    /* *
     * 发送数字验证短信
     *
     * @param  int $mobile 手机号
     * @param  string $template 模板
     * @return boolean
     * */
    public static function send($mobile, $template='default')
    {
        // 初始化
        self::init();

        // 能否发送验证码
        if ( ! Limit::canSendSms($mobile))
            return false;

        // 获取模板配置
        $template = config('sms.templates.' . $template);
        $template = isset($template) ? $template : config('sms.templates.default');

        // 取得并保存验证码
        $code = Limit::makeAndPutSmsCode($mobile);

        // 发送验证码
        if (! Providers\SmsProvider::send($mobile, $code, $template['code'], $template['signName'])) {
            data()->setErr('mobile', '亲，短信服务器升级，请使用其他方式注册或登录。');
            return false;
        }
        // SMS节流计数
        Limit::addSendSmsOkTimes($mobile);

        return true;
    }

    /* *
     * 验证短信
     *
     * @param  int $mobile 手机号
     * @param  int $code 验证码
     * @return boolean
     * */
    public static function check($mobile, $code)
    {
        // 初始化
        self::init();

        if ( ! Limit::canValidSms($mobile))
            return false;

        // 验证短信
        if ( ! Limit::getAndValidSmsCode($mobile, $code))
            return false;

        return true;
    }
}
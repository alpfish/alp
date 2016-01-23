<?php
namespace Alp\Common\Sms\Contracts;

interface Sms
{
    /* *
     * 发送数字验证短信
     *
     * @param  int $mobile 手机号
     * @param  string $template 模板
     * @return Data Response
     * */
    public static function send($mobile, $template='authentication');

    /* *
     * 验证短信
     *
     * @param  int $mobile 手机号
     * @param  int $code 验证码
     * @return Data Response
     * */
    public static function check($mobile, $code);

    // 发送文本短信，暂无此功能需求
    //public static function sendText($mobile, $text);

}
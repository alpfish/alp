<?php

namespace Alp\Common;

use Alp\Common\Email;

class Mobile
{
    /*
     * 使用百度免费API，地址：http://apistore.baidu.com/apiworks/servicedetail/794.html
     *
     * 到个人中心获取$apiKey
     *
     * */

    /* *
     * @return string|null
     * */
    private static function getInfoForMobile($mobile, $type)
    {
        // 配置apiKey
        $apiKey = 'aeec72828c8a82f78b865e3c231afa31';
        if ( ! empty(config(conFile() . 'apiKey.baiDu.mobile')))
            $apiKey = config(conFile() . 'apiKey.baiDu.mobile');

        if ( ! Mobile::isMobile($mobile))
            return null;

        // 初始化一个cURL会话
        $ch = curl_init();
        $url = 'http://apis.baidu.com/apistore/mobilenumber/mobilenumber?phone='.$mobile;
        $header = array('apikey: ' . $apiKey);

        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);

        // 将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 需要获取的URL地址，也可以在curl_init()函数中设置。
        curl_setopt($ch , CURLOPT_URL , $url);

        // 设置连接超时, 即服务器多少s无响应程序便会断开连接（官方说明：在发起连接前等待的时间，如果设置为0，则无限等待。）
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

        // 设置连接成功后cURL接收数据最长时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);

        // 执行HTTP请求并将Json转换为对象
        $rst = json_decode(curl_exec($ch));

        // 出错处理
        if ( ! empty($rst->errNum) || empty($rst)) {
            // 给网站管理员发送邮件
            $err  = '<h1>百度手机API接口没有成功返回地址信息！</h1>';
            if (! empty($rst->errNum))
                $err .= '<h5>错误代号：' . $rst->errNum . '</h5>';
            if (! empty($rst->errMsg))
                $err .= '<h5>错误信息：' . $rst->errMsg . '</h5>';

            Email::sendEmailForWebmaster('【故障】百度手机归属地API出错', $err);
            return null;
        }
        return $type == 'city' ? $rst->retData->city : $rst->retData->province;
    }

    /* *
     * 得到手机所在 省份(四川)
     * @return string|null
     * */
    public static function getProvinceForMobile($mobile)
    {
        return Mobile::getInfoForMobile($mobile, 'province');
    }

    /* *
     * 得到手机所在 城市(市级：绵阳)
     * @return string|null
     * */
    public static function getCityForMobile($mobile)
    {
        return Mobile::getInfoForMobile($mobile, 'city');
    }

    // 判定手机号
    public static function isMobile($mobile)
    {
        $preg_mobile = '/^1[34578][0-9]{9}$/';
        if (preg_match($preg_mobile, $mobile))
            return true;

        return false;
    }
}
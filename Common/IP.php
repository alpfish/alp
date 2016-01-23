<?php

namespace Alp\Common;

class IP
{
    /*
     * 使用百度免费API，地址：http://apistore.baidu.com/apiworks/servicedetail/114.html
     *
     * 到个人中心获取$apiKey
     *
     * */

    /* *
     * 得到IP所在 省份(四川)
     *
     * @return string|null
     * */
    public static function getProvinceForIP($ip)
    {
        return IP::getAdd($ip, 'province');
    }

    /* *
     * 得到IP所在 城市(市级：绵阳)
     *
     * @return string|null
     * */
    public static function getCityForIP($ip)
    {
        return IP::getAdd($ip, 'city');
    }

    /* *
     * 得到IP所在 地址
     *
     * @param  string $ip
     * @param  string $type
     * @return string|null
     * */
    private static function getAdd($ip, $type)
    {
        // 配置apiKey
        $apiKey = 'aeec72828c8a82f78b865e3c231afa31';
        if ( ! empty(config(conFile() . 'apiKey.baiDu.mobile')))
            $apiKey = config(conFile() . 'apiKey.baiDu.mobile');

        // IP有效性
        if( ! filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }
        $ch = curl_init();
        $url = 'http://apis.baidu.com/apistore/iplookupservice/iplookup?ip='.$ip;
        $header = array('apikey: ' . $apiKey);

        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);

        // 设置curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 需要获取的URL地址，也可以在curl_init()函数中设置。
        curl_setopt($ch , CURLOPT_URL , $url);

        // 设置连接超时, 即服务器多少s无响应程序便会断开连接（官方说明：在发起连接前等待的时间，如果设置为0，则无限等待。）
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

        // 设置连接成功后cURL接收数据最长时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);

        // 执行HTTP请求并将Json转换为对象
        $rst = json_decode(curl_exec($ch));

        // 错误处理
        if ( ! empty($rst->errNum) || empty($rst)) {
            // 给网站管理员发送邮件
            $err  = '<h1>百度IP接口没有成功返回地址信息！</h1>';
            if (! empty($rst->errNum))
                $err .= '<h5>错误代号：' . $rst->errNum . '</h5>';
            if (! empty($rst->errMsg))
                $err .= '<h5>错误信息：' . $rst->errMsg . '</h5>';

            Email::sendEmailForWebmaster('【故障】百度IP查询API出错', $err);
            return null;
        }

        return $type == 'city' ? $rst->retData->city : $rst->retData->province;
    }


    /**
     * 获得真实IP
     *
     * @return string|null
     */
    public static function getIP()
    {
        global $ip;

        $unknown = null;

        if(isset($_SERVER)){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }else if(isset($_SERVER['HTTP_CLIENT_IP'])){
                $ip=$_SERVER['HTTP_CLIENT_IP'];
            }else{
                $ip=$_SERVER['REMOTE_ADDR'];
            }
        }else{
            if(getenv('HTTP_X_FORWARDED_FOR')){
                $ip=getenv('HTTP_X_FORWARDED_FOR');
            }else if(getenv('HTTP_CLIENT_IP')){
                $ip=getenv('HTTP_CLIENT_IP');
            }else{
                $ip=getenv('REMOTE_ADDR');
            }
        }

        /* *处理多层代理的情况,或者使用:
         *  if (false !== strpos($ip, ','))
         *      $ip = reset(explode(',', $ip));
         * */
        $ip = preg_match("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : $unknown;

        return $ip;
    }


}
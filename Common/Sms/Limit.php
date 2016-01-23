<?php

namespace Alp\Common\Sms;

use Cache;
use Carbon\Carbon;
use Alp\Common\IP;
use Alp\Common\Mobile;

class Limit
{
    /* *
     * 获取IP
     *
     * @return string $ip
     */
    private static function getIp()
    {
        $ip = '183.221.144.237';
        //$ip = request()->ip();

        return $ip;
    }

    /* *
     * 根据IP和手机号判断客户端能否发送SMS
     *
     * 如有错误信息，则追加到data()中
     *
     * @param  int $mobile
     * @return boolean
     * */
    public static function canSendSms($mobile)
    {
        if (empty(self::getIp())) {
            data()->setErr('mobile', '无法确定你的位置。');
            return false;
        }

        if ( ! Mobile::isMobile($mobile)) {
            data()->setErr('mobile', '不是有效的手机号。');
            return false;
        }

        // 取得限定变量
        $lmt = self::getLimitEnv($mobile);

        // 发送超限
        if (self::isExcessSendTimes($mobile, $lmt))  {
            data()->setErr('mobile', '对不起，获取验证码次数太多，请稍后再试。');
            return false;
        }

        // 在本地区内
        if (! self::isinArea($mobile, $lmt))
            return false;

        return true;
    }

    /* *
     * 能否验证SMS
     *
     * 如有错误信息，则追加到data()中
     *
     * @param  int $mobile
     * @return boolean
     * */
    public static function canValidSms($mobile)
    {
        if (empty(self::getIp())) {
            data()->setErr('mobile', '无法确定你的位置。');
            return false;
        }

        if ( ! Mobile::isMobile($mobile)) {
            data()->setErr('mobile', '不是有效的手机号。');
            return false;
        }

        // 取得限定变量
        $lmt = self::getLimitEnv($mobile);

        // 验证超限
        if (self::isExcessValidTimes($mobile, $lmt)) {
            data()->setErr('mobile', '对不起，超过验证的安全次数，您可以重新获取验证码。');
            return false;
        }

        return true;
    }

    /* *
     * 生成验证码并存入缓存
     *
     * @param  int $mobile
     * @return int $code
     * */
    public static function makeAndPutSmsCode($mobile)
    {
        // 取得限定变量
        $lmt = self::getLimitEnv($mobile);

        // 生成存放 (为增加体验，在限制时间内使用同一个验证码)
        if (Cache::has('SmsCode'.self::getIp().$mobile)) {
            $code = Cache::get('SmsCode'.self::getIp().$mobile);
        }else{
            $code = mt_rand(1000,9999);
            $expiresAt = Carbon::now()->addMinutes($lmt['cLmtSmsLen']);
            Cache::put('SmsCode'.self::getIp().$mobile, $code, $expiresAt);
        }

        return $code;
    }

    /* *
     * 验证短信
     *
     * @param  int $mobile
     * @param  int $code
     * @return bool
     * */
    public static function getAndValidSmsCode($mobile, $code)
    {
        // 检查
        if ( ! Cache::has('SmsCode'.self::getIp().$mobile)) {
            data()->setErr('code', '对不起， 你输入的验证码已过期。');
            return false;
        }

        // 验证
        $cacheCode = Cache::get('SmsCode'.self::getIp().$mobile);
        if ($code != $cacheCode) {
            // 失败计数
            self::addValidSmsTimes($mobile);
            data()->setErr('code', '对不起， 你输入的验证码错误。');
            return false;
        }

        // 成功后销毁
        Cache::forget('SmsCode'.self::getIp().$mobile);
        return true;
    }

    /* *
     * 取得限定变量
     *
     * @return array $limit
     * */
    private static function getLimitEnv($mobile)
    {
        $limit = config('sms.limit');

        // 设置客户端缓存
        $expiresAt = Carbon::now()->addMinutes($limit['cLmtSmsLen']);

        if ( ! Cache::has('ipSendSmsTimes'.self::getIp()))
            Cache::put('ipSendSmsTimes'.self::getIp(), 0, $expiresAt);

        if ( ! Cache::has('ipValidSmsTimes'.self::getIp()))
            Cache::put('ipValidSmsTimes'.self::getIp(), 0, $expiresAt);

        if ( ! Cache::has('mSendSmsTimes'.$mobile))
            Cache::put('mSendSmsTimes'.$mobile, 0, $expiresAt);

        if ( ! Cache::has('mValidSmsTimes'.$mobile))
            Cache::put('mValidSmsTimes'.$mobile, 0, $expiresAt);

        // 设置网站缓存
        $expiresAt = Carbon::now()->addMinutes($limit['webLmtSmsTotLen']);

        if ( ! Cache::has('webLmtSmsTot'))
            Cache::put('webLmtSmsTot', 0, $expiresAt);

        return $limit;
    }

    /* *
     * 发送SMS成功计数器
     * */
    public static function addSendSmsOkTimes($mobile)
    {
        // 客户端
        Cache::increment('ipSendSmsTimes'.self::getIp());
        Cache::increment('mSendSmsTimes'.$mobile);

        // 网站总量
        Cache::increment('webLmtSmsTot');
    }

    /* *
     * 验证SMS计数器
     * */
    public static function addValidSmsTimes($mobile)
    {
        Cache::increment('ipValidSmsTimes'.self::getIp());
        Cache::increment('mValidSmsTimes'.$mobile);
    }

    /* *
     * 发送超限判断
     *
     * @return boolean
     * */
    private static function isExcessSendTimes($mobile, $lmt)
    {
        // 客户端发送超限
        if (Cache::get('ipSendSmsTimes'.self::getIp()) >= $lmt['ipSendSmsTimes'])
            return true;
        if (Cache::get('mSendSmsTimes'.$mobile) >= $lmt['mSendSmsTimes'])
            return true;

        // 网站发送超限
        if (Cache::get('webLmtSmsTot') >= $lmt['webLmtSmsTot'])
            return true;

        return false;
    }

    /* *
     * 验证超限判断
     *
     * @return boolean
     * */
    private static function isExcessValidTimes($mobile, $lmt)
    {
        if (Cache::get('ipValidSmsTimes'.self::getIp()) > $lmt['ipValidSmsTimes'])
            return true;
        if (Cache::get('mValidSmsTimes'.$mobile) > $lmt['mValidSmsTimes'])
            return true;

        return false;
    }

    /* *
     * 判断是否在本地区
     *
     * @return boolean
     * */
    private static function isInArea($mobile, $lmt)
    {
        // 验证IP城市
        if ($lmt['ipAreaLvl'] == '市') {
            $ipArea = IP::getCityForIP(self::getIp());
            // 确定IP模块正常，null表示故障，则不使用此功能
            if (is_null($ipArea))
                goto CHECK_M;

            if ($ipArea != $lmt['ipArea']) {
                data()->setErr('mobile', '亲，你的IP地址不在'. $lmt['ipArea'].'市，推荐你用微信免注册直接登录。');
                return false;
            }
        }

        // 验证IP省份
        if ($lmt['ipAreaLvl'] == '省') {
            $ipArea = IP::getProvinceForIP(self::getIp());
            // 确定IP模块正常，null表示故障，则不使用此功能
            if (is_null($ipArea))
                goto CHECK_M;

            if ($ipArea != $lmt['ipArea']) {
                data()->setErr('mobile', '亲，你的IP地址不在'. $lmt['ipArea'].'省，推荐你用微信免注册直接登录。');
                return false;
            }
        }

        CHECK_M:

        // 验证手机城市
        if ($lmt['mAreaLvl'] == '市') {
            $mArea = Mobile::getCityForMobile($mobile);
            // 确定手机模块正常
            if (is_null($mArea))
                goto END;

            if ($mArea != $lmt['mArea']) {
                data()->setErr('mobile', '亲，你的手机号归属地不在'. $lmt['mArea'].'市，推荐你用微信免注册直接登录。');
                return false;
            }
        }

        // 验证手机省份
        if ($lmt['mAreaLvl'] == '省') {
            $mArea = Mobile::getProvinceForMobile($mobile);
            // 确定手机模块正常
            if (is_null($mArea))
                goto END;

            if ($mArea != $lmt['mArea']) {
                data()->setErr('mobile', '亲，你的手机号归属地不在'. $lmt['mArea'].'省，推荐你用微信免注册直接登录。');
                return false;
            }
        }

        END:

        return true;
    }

}
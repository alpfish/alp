<?php

namespace Alp\Common\Email;
use Mail;

class Email
{
    // 合并配置文件
    private static function init()
    {
        $path = realpath(__DIR__.'/config/email.php');
        $config = app('config')->get('email', []);

        app('config')->set('email', array_merge(require $path, $config));
    }

    /* *
     * 发送邮件给网站管理员
     * */
    public static function sendEmailForWebmaster($title, $content)
    {
        self::init();

        // 发件人
        $from = [
            'add' => config('email.sender'),
            'name' => config('email.webName'),
        ];

        // 收件人
        $to = config('email.webmaster');

        // 视图
        $view = config('email.view.webmaster');

        // 发送视图邮件
        if ( ! empty($view))
        {
            $rst = Mail::send(
                $view,
                ['content' => $content],
                function ($message) use ($from, $to, $title){
                    $message->from($from['add'], $from['name'])
                            ->to($to)
                            ->subject($title);
            });

        }
        else
        {
            // 发送文本邮件
            $content = strip_tags($content);
            $rst = Mail::raw(
               $content,
               function ($message) use ($to, $title){
                    $message->to($to)
                            ->subject($title);
            });
        }


        if (! $rst)
        {
            // TODO 邮件发送错误日志
        }

        return null;
    }
}
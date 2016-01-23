<?php

/**-------------------------------------------------
 *                 帮助函数使用说明
 * -------------------------------------------------
 *
 * 在根目录的 composer.json 文件中 "autoload": {} 里加入：
 *         "files": [
 *                   "app/helpers.php"
 *           ]
 * 运行：composer dumpauto
 *
 */

if ( ! function_exists('model'))
{
    function model($model)
    {
        // 配置模型命名空间
        $config = [
            'User' => App\Models\UserSystem\User\User::class,
        ];

        if (! array_key_exists($model, $config))
            throw new InvalidArgumentException('帮助函数model()中没有指定的模型，或检查参数类命名规则是否正确。');

        // 拼接模型字符串
        $class = '\\'.ltrim($config[$model], '\\');

        return new $class;// 建立模型(此new是从字符串中获得指定的模型类，并非从类中新建一个实例)
    }
}

if ( ! function_exists('data'))
{
    function data()
    {
        // API: 数据封装及基本响应
        return \Alp\Api\Data\Data::getInstance();
    }
}

if ( ! function_exists('jwt'))
{
    function jwt()
    {
        // API: JSON WEB TOKEN
        return \Alp\Api\JWT\JWT::getInstance();
    }
}

if ( ! function_exists('power'))
{
    /**
     * Power::当作power()->函数使用
     *
     * @return mixed
     */
    function power()
    {
        return app('power');
    }
}

if ( ! function_exists('webName'))
{
    /**
     * 计时器（单位：秒）
     *
     * @return float
     */
    function webName()
    {
        $webName = '';
        if ( ! empty(config(conFile() . 'webName')))
            $webName = config(conFile() . 'webName');
        return $webName;
    }
}

if ( ! function_exists('microtime_float'))
{
    /**
     * 计时器（单位：秒）
     *
     * @return float
     */
    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}


























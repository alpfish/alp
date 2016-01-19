<?php
namespace Alp\Api;

use Alp\Api\Data\Data;
use Alp\Api\JWT\JWT;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{

    /**
     * 不延迟加载
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * @return void
     */
    public function boot()
    {
        // 合并JWT配置文件
        $this->mergeConfig();
        // JWT初始化
        JWT::init();
    }

    /**
     * @return void
     */
    public function register()
    {
        //已在帮助函数jwt()和data()中单例绑定JWT和Data，所以这里注释掉相关注册
        //$this->JWTRegister();
        //$this->DataRegister();
    }

    /* *
     * 合并JWT配置文件
     *
     * @return void
     */
    protected function mergeConfig()
    {
        $path = realpath(__DIR__.'/jwt/config/jwt.php');
        $config = $this->app['config']->get('jwt', []);

        $this->app['config']->set('jwt', array_merge(require $path, $config));
    }

    /**
     * 注册 Api Data
     */
    protected function DataRegister()
    {
        // 绑定实例(Data中已为单例模式)
        $this->app->instance('jwt', Data::getInstance());
    }

    /* *
     * 注册 Api JWT
     * */
    protected function JWTRegister()
    {
        // 绑定实例(JWT中已为单例模式)
        $this->app->instance('jwt', JWT::getInstance());
    }

}
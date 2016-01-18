<?php
namespace Alp\Api;

use Alp\Api\Data\Data;
use Alp\Api\JWT\JWTProvider;
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
     * 引导逻辑
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfig();
        JWTProvider::init();
    }

    /**
     * 注册所有
     *
     * @return void
     */
    public function register()
    {
        $this->JWTRegister();
        $this->DataRegister();
    }

    /* *
     * 合并JWT配置文件
     *
     * @return void 可以使用config('jwt')帮助函数
     */
    protected function mergeConfig()
    {
        $path = realpath(__DIR__.'/jwt/config/jwt.php');
        $config = $this->app['config']->get('jwt', []);

        $this->app['config']->set('jwt', array_merge(require $path, $config));
    }

    /**
     * Api Data数据封装与基本响应 注册
     */
    public function DataRegister()
    {
        $this->app->singleton('data', function(){
            return new Data;
        });
    }

    /* *
     * Api JWT注册
     * */
    public function JWTRegister()
    {
        // 别名
        $this->app->alias('jwt', 'Alp\Api\JWT\Contracts\JWT');

        // 单例
        $this->app->singleton('jwt', function(){
            return new JWTProvider;
        });
    }

}
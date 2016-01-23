<?php

namespace Alp\Power;

use Blade;
use Illuminate\Support\ServiceProvider;

class PowerServiceProvider extends ServiceProvider
{
    // --------------------------------------------------------------------
    //     Power对已认证用户的权限判断, User使用Traits对所有用户的权限判断和处理
    // --------------------------------------------------------------------

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
        $this->registerBladeExtensions();
    }

    /**
     * 注册所有
     *
     * @return void
     */
    public function register()
    {
        $this->registerPower();
        $this->registerFacade();
        $this->registerBindings();

    }

    /**
     * 绑定服务
     *
     * @return void
     */
    private function registerPower()
    {
        $this->app->bind('power', function($app){
            return new Power($app);
        });
    }

    /**
     * 注册门面(一般将门面放在config/app.php中注册)
     *
     * @return void
     */
    public function registerFacade()
    {
        $this->app->booting(function() {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Power', \Alpfish\Power\Facades\Power::class);
        });
    }

    /**
     * 绑定接口
     *
     * @return void
     */
    public function registerBindings()
    {
        $this->app->bind(\Alpfish\Power\Contracts\Power::class, function($app){
            return new Power($app);
        });
    }

    /**
     * Blade模板中使用 @may() 和 @endmay ; 如用 @can()，请参照 may() 根定义注释。
     * 模板中使用
     *
     * @return void
     */
    protected function registerBladeExtensions()
    {
        /* *
         * @may 配合 @endif @else 等一起使用，可嵌套在@if(@may('..'))中
         *
         * 举例：
         *
         * @may('login_backend')
         *    当前用户可以登录后台。
         * @else
         *    不可登录。
         * @endif
         *
         * */
        Blade::directive('may', function($perm) {
            return "<?php if (Power::may{$perm}): ?>";
        });

    }
}


/*
|--------------------------------------------------------------------------
|                               角色权限管理
|--------------------------------------------------------------------------
*/
---------
功能：
---------
1. 判断角色

2. 判断权限

---------
依赖：
---------
User模型

---------
接口：
---------

1. power()帮助函数

   用法:power()->may('login_backend'); //判断当前用户是否有'login_backend'的权力
   全局帮助函数，需在helpers.php中定义
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

2. Power门面(类似使用静态方法)

   用法:Power::may('login_backend');
   注意:使用前use Power;

3. 依赖注入

   在此包中很少使用，PowerServiceProvider绑定契约是做学习参考用

4. Blade模板

   @may('login_backend')
       当前用户可以登录后台。
   @endif



---------
配置：
---------
1. 建表建Models(见.res/DB/)
2. 将Role与User多对多的关系添加进User
3. 在User模型中：use Alp\Power\Traits\UserPowerTrait;
4. 在config/app.php注入服务提供